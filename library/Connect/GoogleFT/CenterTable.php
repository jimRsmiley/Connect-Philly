<?php

require_once('fusion-tables-client-php/clientlogin.php');
require_once('fusion-tables-client-php/sql.php');
require_once('fusion-tables-client-php/file.php');

// @todo figure out how to get 3rd party apps in the zend autoloader
/**
 * because Google Fusion Tables don't include the rowid in cases when using
 * "SELECT *", this class builds a list of column names on constructing, then
 * cats all the names along with rowid into the select statement 
 */
class Connect_GoogleFT_CenterTable {

    protected $_ftclient;
    protected $_tableId;
    protected $_locationColumn = 'Longitude';
    protected $_columnNames;
    
    public function __construct($tableId, $user, $pass) {

        $this->logger = Zend_Registry::get('Log');

        if ( empty($tableId) ) {
            throw new ZendException("tableId must be defined");
        }

        $token = ClientLogin::getAuthToken($user, $pass);
        $ftclient = new FTClientLogin($token);

        $this->_tableId = $tableId;
        $this->_ftclient = $ftclient;
        
        $this->_columnNames = $this->getColumnNames();
    }

    /**
     * tries to insert data into the google fusion table, returns false on
     * failure.
     * 
     * It adds the current timestamp to the computer center when entered.
     *
     * @param mixed $data - an associative array
     * @return boolean
     */
    public function insert($data) {

        $timestamp = date('Y-m-d H:i:s');

        $data['Timestamp'] = $timestamp;

        if (!$data) {
            Connect_FileLogger::err(self::logPrefixString(__FUNCTION__) . ': data is null');
            return false;
        }

        $ftclient = $this->_ftclient;

        $result = $ftclient->query(
                SQLBuilder::insert($this->_tableId, $data)
        );

        if (preg_match('/Error 400/', $result)) {
            Connect_FileLogger::err(self::logPrefixString(__FUNCTION__) . ': error 400');

            Connect_FileLogger::err(self::logPrefixString(__FUNCTION__) . ': sql => ' .
                    SQLBuilder::insert($this->_tableId, $data));

            Connect_FileLogger::err(self::logPrefixString(__FUNCTION__) . Zend_Debug::dump($data, false));

            return false;
        }

        return true;
    }

    /**
     *
     * returns an array of column information as 
     *     [i] => Array
        (
            [column id] => col45
            [name] => Sunday Hours Close
            [type] => datetime
        )
     * @return array 
     */
    public function getColumnInfo() {

        $ftclient = $this->_ftclient;

        $result =
                $ftclient->query(SQLBuilder::describeTable($this->_tableId));

        return $this->toAssocArray($result);
    }

    /**
     *
     * @return array the column names of the fusion table 
     */
    public function getColumnNames() {
        $result = $this->getColumnInfo();
        
        $names = array();
        foreach( $result as $rowInfo ) {
            array_push( $names, $rowInfo['name'] );
        }
        
        return $names;
    }
    
    public function getCenters( Connect_CenterRequest $request,
            $limit = 1, $offset = null ) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";

        if( $request->getLatitude() ==  null ) {
            throw new InvalidArgumentException( 'latitude may not be null' );
        }
        if( $request->getLongitude() == null ) {
            throw new InvalidArgumentException( 'longitude may not be null' );
        }
        
        $sql = $this->getSQL( $request, $limit, $offset );

        /*
         * make the call to the fusion table
         */
        $result = $this->makeAPICall($sql);

        $centers = self::createCenters( $result, $request );
        Connect_FileLogger::info($logPrefix . "returning " . count($centers) . " centers");

        return $centers;
    }

    public function getSQL( Connect_CenterRequest $request, 
            $limit = null, $offset = null ) {
        $conditions = "'Pending Confirmation' NOT EQUAL TO 'true' AND";

        $limitStr = '';
        if ( !empty($limit) ) {
            $limitStr = " LIMIT $limit";
        }

        $offsetStr = '';
        if ( !empty($offset) ) {
            $offsetStr = " OFFSET $offset";
        }

        $orderBy = sprintf(
                " ORDER BY ST_DISTANCE('%s', LATLNG( %s, %s ) )%s%s", 
                $this->_locationColumn, 
                $request->getLatitude(), $request->getLongitude(), 
                $offsetStr, $limitStr);

        

        if ($request->getSearchOptions() != null) {
            foreach ($request->getSearchOptions() as $term) {
                
                if( strtolower($term) != 'open' ) {
                    $conditions .= ' '
                            . Connect_GoogleFT_SearchTerms::getFtSql($term)
                            . ' AND';
                }
            }
        }
        // get rid of that last trailing ' AND'
        if ($conditions != null) {
            $conditions = substr($conditions, 0, strlen($conditions) - 4);
        }

        $cols = $this->_columnNames;
        
        $sql = SQLBuilder::select($this->_tableId, $cols, $conditions, $orderBy);
        
        return $sql;
    }
    
    /**
     * handles the fusion table api calls and checks the result for errors
     * 
     * @param type $sql
     * @return type 
     */
    public function makeAPICall($sql) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";
        Connect_FileLogger::info($logPrefix . $sql);
        $csv = $this->_ftclient->query($sql);

        if (preg_match("/(Parse error near.*)\n/", $csv, $match)) {
            $msg = $sql . "\n" . $match[1];

            throw new FusionTableException($msg);
        }
        else if( preg_match("/<H2>Error \d*<\/H2>/", $csv ) ) {
            throw new FusionTableException($csv);
        }

        return $csv;
    }

    /**
     * given the fusion table result, breaks up the csv format into computer center objects
     * @param type $csv the result of a sql query from the fusion table API
     * @return array containing the computer centers
     */
    protected static function createCenters(
                                    $csv, Connect_CenterRequest $request) {

        // break it up by lines, each line is a computer center
        $lines = explode("\n", $csv);

        // the first line is the column names
        $colNames = explode(',', $lines[0]);

        $computerCenters = array();

        for ($i = 1; $i < count($lines); $i++) {
            // probably at the end of the result
            if (preg_match("/^\s*$/", $lines[$i])) {
                break;
            }

            $centerArray = str_getcsv($lines[$i]);
            $assoc_array = array_combine($colNames, $centerArray);

            $foundCenter = new Connect_FoundCenter($assoc_array, $request );
            array_push( $computerCenters, $foundCenter );
        }

        return $computerCenters;
    }

    protected function toAssocArray($result) {

        $lines = explode("\n", $result);
        $columnNames = explode(',', $lines[0]);

        $retArray = array();
        for ($i = 1; $i < count($lines); $i++) {

            if (empty($lines[$i])) {
                break;
            }

            $elements = explode(',', $lines[$i]);

            $obj_array = array();
            for ($j = 0; $j < count($elements); $j++) {
                $obj_array[$columnNames[$j]] = $elements[$j];
            }
            array_push($retArray, $obj_array);
        }

        return $retArray;
    }

    /**
     * returns a string of the class name and function name for log strings
     * @return string
     */
    protected static function logPrefixString($functionName) {
        return __CLASS__ . '->' . $functionName;
    }

}

class FusionTableException extends Zend_Exception {
    
}

?>

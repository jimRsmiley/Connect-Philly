<?php

require_once('fusion-tables-client-php/clientlogin.php');
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
    protected static $_locationColumn = 'Longitude';
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
     * insert data into the google fusion table
     * 
     * It adds the current timestamp to the computer center when entered.
     *
     * @param mixed $data - an associative array
     * @return boolean false on failure
     */
    public function insert($data) {

        $timestamp = date('Y-m-d H:i:s');

        $data['Timestamp'] = $timestamp;

        if( empty( $data ) ) {
            throw new InvalidArgumentException( 'data may not be null' );
        }

        $ftclient = $this->_ftclient;
        $sql = Connect_GoogleFT_SQLBuilder::insert($this->_tableId, $data);
        $result = $ftclient->query( $sql );              

        if (preg_match('/Error 400/', $result)) {
            throw new Exception($result);
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
                $ftclient->query(Connect_GoogleFT_SQLBuilder::describeTable($this->_tableId));

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
    
    public function getSelectStatement( Connect_Position $position, 
            $searchOptions = null,
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
                self::$_locationColumn, 
                $position->getLat(), $position->getLng(), 
                $offsetStr, $limitStr);

        

        if ($searchOptions != null) {
            foreach ($searchOptions as $term) {
                
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
        
        $sql = Connect_GoogleFT_SQLBuilder::select(
                        $this->_tableId, $cols, $conditions, $orderBy);
        
        return $sql;
    }
    
    /**
     * handles the fusion table api calls and checks the result for errors
     * 
     * @param string $sql
     * @return string a csv containing the computer centers 
     */
    public function makeAPICall($sql) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";
        Connect_FileLogger::info($logPrefix . $sql);
        $csv = $this->_ftclient->query($sql);

        if (preg_match("/(Parse error near.*)\n/", $csv, $match)) {
            $msg = $sql . "\n" . $match[1];

            throw new CenterTableException($msg);
        }
        else if( preg_match("/<H2>Error \d*<\/H2>/", $csv ) ) {
            throw new CenterTableException($csv);
        }

        return $csv;
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
}



?>

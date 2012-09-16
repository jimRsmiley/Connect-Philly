<?php

require_once('fusion-tables-client-php/clientlogin.php');
require_once('fusion-tables-client-php/file.php');

// @todo figure out how to get 3rd party apps in the zend autoloader
/**
 * Description of FusionTable
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_GoogleFT_FusionTable {
    
    protected $_ftclient;
    protected $_tableId;
    protected $_columnInfo;


    public function __construct($tableId, $user, $pass) {

        $this->logger = Zend_Registry::get('Log');

        if ( empty($tableId) ) {
            throw new ZendException("tableId must be defined");
        }

        $token = ClientLogin::getAuthToken($user, $pass);
        $ftclient = new FTClientLogin($token);

        $this->_tableId = $tableId;
        $this->_ftclient = $ftclient;
        
        // need to get the column names so we can 
        //$this->_columnNames = $this->getColumnNames();
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

        if( $this->_columnInfo == null ) {
            
        
            $ftclient = $this->_ftclient;

            $result =
                    $ftclient->query(Connect_GoogleFT_SQLBuilder::describeTable($this->_tableId));
        
            $this->_columnInfo = $this->toAssocArray($result);
            
        }
        
        return $this->_columnInfo;
    }

    /**
     *
     * @return array the column names of the fusion table 
     */
    public function getColumnNames() {
        $columnInfo = $this->getColumnInfo();
        
        $names = array();
        foreach( $columnInfo as $rowInfo ) {
            array_push( $names, $rowInfo['name'] );
        }
        
        return $names;
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

            throw new Connect_GoogleFT_FusionTableException($msg);
        }
        else if( preg_match("/<H2>Error \d*<\/H2>/", $csv ) ) {
            throw new Connect_GoogleFT_FusionTableException($csv);
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

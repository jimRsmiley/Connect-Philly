<?php

//require_once('fusion-tables-client-php/clientlogin.php');
require_once('google-api-php-client/src/Google_Client.php');
require_once('google-api-php-client/src/contrib/Google_FusiontablesService.php');

// @todo figure out how to get 3rd party apps in the zend autoloader
/**
 * Description of FusionTable
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_GoogleFT_FusionTable {
    
    protected $fusionTableService;
    protected $_tableId;
    protected $_columnInfo;


    public function __construct( $options ) {

        if ( !array_key_exists( 'tableId', $options ) || empty( $options['tableId'] ) ) {
            throw new ZendException("tableId must be defined");
        }
        
        $this->_tableId = $options['tableId'];
        $clientId       = $options['clientId'];
        $emailAddress   = $options['emailAddress'];
        $privateKeyFile = $options['privateKeyFile'];
        
        if( !file_exists($privateKeyFile) )
            throw new \InvalidArguementException( "privateKeyFile does not exist" );
        
        $this->logger = Zend_Registry::get('Log');

        if( !isset($apiConfig) ) {
            include('google-api-php-client/src/config.php');
        }
        
        if( !defined( 'EMAIL_ADDRESS' ) )
            define( 'EMAIL_ADDRESS', '846924947206-2lc7bguo7la691sk5uk0842kofitp6ft@developer.gserviceaccount.com' );
        if( !defined('CLIENT_ID') )
            define( 'CLIENT_ID', '846924947206-2lc7bguo7la691sk5uk0842kofitp6ft.apps.googleusercontent.com' );

        $client = new Google_Client();
        $client->setApplicationName('Connect Philly');

        // set assertion credentials
        $client->setAssertionCredentials(
          new Google_AssertionCredentials(
            $emailAddress, // email you added to GA
            array('https://www.googleapis.com/auth/fusiontables'),
            file_get_contents($privateKeyFile)  // keyfile you downloaded

        ));

        $client->setClientId($clientId);
        $client->setAccessType('online'); // default: offline
        
        $this->fusionTableService = new Google_FusiontablesService($client);
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

            $result =
                    $this->fusionTableService->query
                        ->sql(
                                Connect_GoogleFT_SQLBuilder
                                    ::describeTable($this->_tableId)
                    );
            
            $this->_columnInfo = array();
            foreach( $result['rows'] as $row ) {
                $arr = array(
                    'column id' => $row[0],
                    'name'      => $row[1],
                    'type'      => $row[2]
                );
                $this->_columnInfo[] = $arr;
            }
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
        
        //print $sql."\n";
        $result = $this->fusionTableService->query->sql($sql);

        if( $result == null )
            throw new Connect_GoogleFT_FusionTableException($msg);

        return $result;
    }


    protected function toAssocArray($result) {

        if( !is_string($result) ) {
            throw new \InvalidArguementException( "result must be a string" );
        } 
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

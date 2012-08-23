<?php

/**
 * Map Computer Center objects to the current choice of database and handle
 * all interactions with said database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_ComputerCenterMapper {
    
    protected $table;
    
    public function __construct() {
        $this->table = $this->getFusionTable();
    }
    
    public function save(Connect_ComputerCenter $computerCenter)
    {
        $data = array();
        
        foreach ($computerCenter->getOptions() as $key => $value ) {
                
            if( isset( $value ) && !preg_match( "/^\s*$/", $value ) ) {

                $method = 'get'.$key;
                // add a space in front of the capital letters
                $key = preg_replace('/(?<!\ )[A-Z]/', ' $0', $key);
                $key = ucfirst( $key );

                $data[$key] = addslashes( $computerCenter->$method() );
            }
        }
        
        $data['Timestamp'] = date('Y-m-d H:i:s');
        
        return $this->getFusionTable()->insert($data);
    }
    
    /**
     *
     * @param Connect_Position $position
     * @param type $searchOptions
     * @param type $testTime
     * @param type $numCenters
     * @param type $offset
     * @return array
     * @throws InvalidArgumentException 
     */
    public function getOpenCenters( 
            Connect_Position $position, 
            $searchOptions,
            $testTime,
            $numCenters = 1, 
            $offset = 0 ) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";

        if( empty($testTime) ) {
            throw new InvalidArgumentException('test time may not be null');
        }
        
        Connect_FileLogger::info( $logPrefix . "looking for a total of $numCenters centers after we pass by $offset centers" );
        
        $fetchLimit = 50;
        $sqlOffset = 0;

        $returnCenters = array();
        $limitReached = false;
        
        while ( !$limitReached && ($centers
                = self::getCenters($position,$searchOptions, $fetchLimit, $sqlOffset) ) ) 
        {
            // pull out the open centers from $centers
            $openCenters = self::getCurrentlyOpenCenters( $centers, $testTime );
            
            for( $i = 0; $i < count($openCenters); $i++ ) {

                if ( !$limitReached ) 
                {
                    // don't count any centers before the offset
                    if( $i < $offset ) {
                        //Connect_FileLogger::info( $logPrefix . "counter $i < offset $offset" );
                        Connect_FileLogger::info( $logPrefix . "$i open centers encountered so far, haven't reached the start recording minimum of $offset; passing " . $openCenters[$i]->getLocationTitle() );
                         
                    }
                    else {
                        Connect_FileLogger::info($logPrefix . "$i open center found, adding to openCenters: " . $openCenters[$i]->getLocationTitle());
                        
                        array_push( $returnCenters, $openCenters[$i] );
                        Connect_FileLogger::info( $logPrefix . '$retVal size is now ' . count( $returnCenters ) );
                        
                        Connect_FileLogger::info( $logPrefix . "counter $i limit $numCenters" );
                    }

                    if ( count($returnCenters) == $numCenters ) {
                        Connect_FileLogger::info( $logPrefix . "limit reached" );
                        $limitReached = true;
                    }
                    
                }
            } // end foreach center
            $sqlOffset += $fetchLimit;
        } // end while there are more centers

        return $this->createFoundCenters($returnCenters,$position);
    }
    
    /**
     * given computer centers, returns all centers open during $timestamp
     * @param array $centers
     * @return array 
     */
    public function getCurrentlyOpenCenters( $centers, $timestamp ) {
        
        $openCenters = array();
        
        foreach( $centers as $center ) {
            
            if( $center->isOpen($timestamp) ) {
                array_push( $openCenters, $center );
            }
        }
        
        return $openCenters;
    }
    
    public function getCenters( Connect_Position $position, 
            $searchOptions = null,
            $numCenters = 1, 
            $offset = null ) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";

        $this->validatePosition($position);
        
        $sql = $this->table->getSelectStatement( $position, $searchOptions, $numCenters, $offset );

        /*
         * make the call to the fusion table
         */
        $result = $this->table->makeAPICall($sql);

        $centers = self::createCenters( $result );
        Connect_FileLogger::info($logPrefix . "returning " . count($centers) . " centers");

        return $this->createFoundCenters($centers,$position);
    }
    
    public function getCenterByRowid( $rowid ) {
        
        if( empty( $rowid ) ) {
            throw new InvalidArgumentException('rowid may not be null' );
        }
        
        $cols = null;
        $conditions = "ROWID = '$rowid'";
        
        $sql = Connect_GoogleFT_SQLBuilder::select(
                    Zend_Registry::get('configuration')->gmap->FusionTableId, 
                    $cols,
                    $conditions
                );
        
        $result = $this->table->makeAPICall($sql);

        $centers = $this->createCenters($result);

        if( $centers != null ) {
            return $centers[0];
        }
        else {
            return null;
        }
    }
    
    /**
     * given the fusion table result, breaks up the csv format into 
     * computer center objects
     * @param type $csv the result of a sql query from the fusion table API
     * @return array containing the computer centers
     */
    protected static function createCenters($csv) {

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

            array_push( 
                    $computerCenters, 
                    new Connect_ComputerCenter($assoc_array) 
                );
        }

        return $computerCenters;
    }
    
    protected static function getFusionTable() {
        $config = Zend_Registry::get('configuration');
        
        return new Connect_GoogleFT_CenterTable( 
                $config->gmap->FusionTableId, 
                $config->google->user,
                $config->google->pass
              );
    }
    
    public function validatePosition($position) {

        if( $position == null ) {
            throw new InvalidArgumentException('position may not be null');
        }
        else if( $position->getLat() ==  null ) {
            throw new InvalidArgumentException( 'latitude may not be null' );
        }
        else if( $position->getLng() == null ) {
            throw new InvalidArgumentException( 'longitude may not be null' );
        }
    }
    
    /**
    * this function wraps the computer centers up into a found center array
     * and returns it
    * @param array $centers an array of computer centers
    * @param Connect_Position $position 
    */
    public function createFoundCenters($centers, Connect_Position $position) {

        $returnCenters = array();
        
        foreach( $centers as $center ) {
            $options = $center->getOptions();
            $options['searchPosition'] = $position;
            
            $foundCenter = new Connect_FoundCenter( $options );
            array_push( $returnCenters, $foundCenter );
        }
        
        return $returnCenters;
    }
}
?>

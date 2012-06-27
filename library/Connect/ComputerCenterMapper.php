<?php

/**
 * Map Computer Center objects to the current choice of database and handle
 * all interactions with said database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_ComputerCenterMapper {
    
    public static function getCenters( Connect_CenterRequest $request, 
            $limit = null, $offset = null ) {
        
        if( $request->getTestIsOpen() ) {
            return self::getOpenCenters( $request, $limit, $offset );
        }
        else {
            return self::getOtherCenters( $request, $limit, $offset );
        }
    }
    
    public static function insert( Connect_ComputerCenter $center ) {
        
    }
    
    
    /**
     * @todo this function is a mess
     * @param Connect_CenterRequest $request
     * @param type $timestamp
     * @param int $limit the number of centers to return
     * @param type $offset the number of centers to skip before collecting
     * centers to return
     * @return array the array of open centers
     */
    public static function getOpenCenters( Connect_CenterRequest $request,
                    $limit = 1, $offset = 0 ) {
        $logPrefix = __CLASS__ . "->" . __FUNCTION__ . ": ";

        if( $request->getTestTime() == null ) {
            throw new InvalidArgumentException('test time may not be null');
        }
        
        // @todo should the mapper be fixing the lng,lat
        if( $request->getLatitude() == null && $request->getLongitude() == null ) {
            $position = Connect_PhillyGeocoder::geocode($request->getAddress1() );
            $request->setLatitude( $position['latitude'] );
            $request->setLongitude( $position['longitude'] );
        }
        
        Connect_FileLogger::info( "limit=$limit; offset=$offset" );
        
        $fetchLimit = 50;
        $sqlOffset = 0;

        $retVal = array();
        $limitReached = false;
        
        while ( !$limitReached && ($centers
                = self::getOtherCenters($request, $fetchLimit, $sqlOffset) ) ) 
        {
            // pull out the open centers from $centers
            $openCenters = self::getCurrentlyOpenCenters( $centers, $request->getTestTime() );
            
            for( $i = 0; $i < count($openCenters); $i++ ) {

                if ( !$limitReached ) 
                {
                    // don't count any centers before the offset
                    if( $i < $offset ) {
                        Connect_FileLogger::info( $logPrefix . "counter $i < offset $offset" );
                    }
                    else {
                        Connect_FileLogger::info($logPrefix . "$i open center found, adding to openCenters: " . $openCenters[$i]->getLocationTitle());
                        
                        array_push( $retVal, $openCenters[$i] );
                        Connect_FileLogger::info( $logPrefix . '$retVal size is now ' . count( $retVal ) );
                        
                        Connect_FileLogger::info( $logPrefix . "counter $i limit $limit" );
                    }

                    if ( count($retVal) == $limit ) {
                        Connect_FileLogger::info( $logPrefix . "limit reached" );
                        $limitReached = true;
                    }
                    
                }
            } // end foreach center
            $sqlOffset += $fetchLimit;
        } // end while there are more centers

        return $retVal;
    }
    
    public static function getOtherCenters( Connect_CenterRequest $request,
            $limit = 1, $offset = null ) {
        $ftclient = self::getFusionTable();
        
        // @todo should the mapper be fixing the lng,lat
        if( $request->getLatitude() == null && $request->getLongitude() == null ) {
            $position = Connect_PhillyGeocoder::geocode($request->getAddress1() );
            $request->setLatitude( $position['latitude'] );
            $request->setLongitude( $position['longitude'] );
        }
        
        return $ftclient->getCenters( $request, $limit, $offset );
    }
    
    /**
     * given computer centers, returns all centers open during $timestamp
     * @param array $centers
     * @return array 
     */
    public static function getCurrentlyOpenCenters( $centers, $timestamp ) {
        
        $openCenters = array();
        
        foreach( $centers as $center ) {
            if( self::isCenterOpen( $center, $timestamp ) ) {
                array_push( $openCenters, $center );
            }
        }
        
        return $openCenters;
    }
    
    public static function isCenterOpen( $center, $timestamp ) {
        return $center->getOpenStatus($timestamp)
                        == Connect_ComputerCenter_OpenStatus::$OPEN;
    }
    
    protected static function getFusionTable() {
        $config = Zend_Registry::get('configuration');
        
        return new Connect_GoogleFT_CenterTable( 
                $config->gmap->FusionTableId, 
                $config->google->user,
                $config->google->pass
              );
    }
}

?>

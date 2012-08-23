<?php

/**
 * Description of ResponseBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_SMS_ResponseBuilder {
    
    public static function create( Connect_CenterRequest $request, 
                                $numCenters = 1, $nextCenterOffset = null ) {
        
        if( empty($request) ) {
            throw new IllegalArgumentException( __METHOD__ 
                    . '$request cannot be empty' );
        }
        
        /*
         * help request
         */
        if( $request instanceof Connect_SMS_Request_Help ) {
            return new Connect_SMS_Response_Help();
        }
        
        $position = Connect_PhillyGeocoder::geocode($request->getAddress1() );
        
        if( $position == null ) {
            return new Connect_SMS_Response_BadCenterRequest($request);
        }
        
        $request->setLatitude( $position->getLat() );
        $request->setLongitude( $position->getLng() );
        
        // null or 1+ next center
        $nextCenterOffset = self::getNextCenterNum($request);
        $foundCenter = null;
        
        try {
            $centerMapper = new Connect_ComputerCenterMapper();
            
            if( $request->getTestIsOpen() ) {
                $foundCenters = $centerMapper->getOpenCenters( 
                                    $position, 
                                    $request->getSearchOptions(),
                                    $request->getTestTime(),
                                    $numCenters, 
                                    $nextCenterOffset 
                                );
            }
            else {
                // pass it to the regular response builder
                $foundCenters = $centerMapper->getCenters( 
                        $position, 
                        $request->getSearchOptions(), 
                        $numCenters, 
                        $nextCenterOffset 
                    );
            }
            
            if( count($foundCenters) > 0 ) {
                $foundCenter = $foundCenters[0];
            }
        }
        catch( Connect_Exception $e ) {
            return new Connect_SMS_Response_BadCenterRequest($request);
        }
        
        /*
         * if they were sending next center number requests and we've run out
         * of them
         */
        if( self::didNextCenterRequestFail($foundCenter,$nextCenterOffset ) ) {
            return new Connect_SMS_Response_NoNextCenter();
        }
        
        /*
         * otherwise we found the center, build a found center response
         */
        else {
            return new Connect_SMS_Response_FoundCenter(
                $request,$foundCenter);
        }
    }
    
    public static function getCenter( $position, $options, $nextCenterNum ) {
        
    }
    
    public static function didNextCenterRequestFail( 
            $foundCenter, $nextCenterNum ) 
    {
        return ( empty($foundCenter) && !empty( $nextCenterNum ) );
    }
    
    public static function getNextCenterNum( Connect_CenterRequest $request ) {

        if( $request instanceof Connect_SMS_Request_NextCenterRequest ) {
            //return $request->getNextCenterNum()+1;
            return $request->getNextCenterNum();
        }
        
        return null;
    }
}
?>

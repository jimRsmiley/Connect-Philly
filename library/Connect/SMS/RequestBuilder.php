<?php

/**
 * taking an inbound message, builds a proper center request
 *
 * If a message contains only digits and is 5 digits long, the system will
 * regard that as a zip code and try to geocode it.
 * 
 * @author JimS
 */
class Connect_SMS_RequestBuilder {
    
    public static function create( 
            Connect_SMS_InboundMessage $inboundMessage, $testTime = null ) {

        $centerRequest = null;
        
        // trim the message content
        $inboundMessage->setMessage( trim( $inboundMessage->getMessage() ) );
        
        $message = $inboundMessage->getMessage();

        /*
         * help message
         */
        if( self::isHelpRequest( $inboundMessage->getMessage() ) ) 
        {
            return new Connect_SMS_Request_Help();
        }
        
        /*
         * a zip code or full address
         */
        else if( self::isZipCode( $inboundMessage->getMessage() ) 
                || !self::isNextCenterRequest($message) ) 
        {
            $centerRequest = new Connect_CenterRequest();
            $message = $inboundMessage->getMessage();
        }
        
        // must be next center request
        else
        {
            $centerRequest = new Connect_SMS_Request_NextCenterRequest();
            
            $nextCenterNum = $inboundMessage->getMessage();
            $centerRequest->setNextCenterNum( $nextCenterNum );
            
            // find the address in the past message text file
            $message = Connect_SMS_PastMessageFile::getLastEntry( 
                                        $inboundMessage->getSenderAddress() );
            
            Connect_FileLogger::info( __CLASS__ . 
                    " nextCenterNum='$nextCenterNum' last valid message='$message'");
            
            // oops, there was no last address
            if( empty( $message ) ) {
                $response = new Connect_SMS_Response_NoNextCenter();
                
                throw new Connect_Exception( $response->getMessage() );
            }
        }

        $centerRequest->setTestIsOpen( self::testIsOpen($message) );
            
        // if we're not testing the time now
        if( empty($testTime) ) {
            $centerRequest->setTestTime( time() );
        }
        else {
            $centerRequest->setTestTime( $testTime );
        }
            
        $message = self::scrubMessage($message, 'open');
        $searchTerms = self::getSearchTerms( $message );
        $address = self::scrubOfSearchTerms($message);
        
        // if we're here, we made it
        $centerRequest->setAddress1( $address );
        $centerRequest->setSearchOptions( $searchTerms );
        
        return $centerRequest;
    }
    
    /**
     * searches the message for valid search terms
     * @param string $message
     * @return array the array of terms that were matched
     */
    protected static function getSearchTerms( $message ) {
        $searchTerms = Connect_GoogleFT_SearchTerms::getSearchTerms();
        $matchedTerms = array();
                
        foreach( $searchTerms as $term ) {
            if( preg_match( "/$term/i", $message ) ) {
                array_push( $matchedTerms, $term );
            }
        }
        
        return $matchedTerms;
    }
    
    /**
     * scrubs the message of all search terms
     * @param string $message the message to be scrubbed
     * @return string scrubbed message
     */
    protected static function scrubOfSearchTerms( $message ) {
        $searchTerms = Connect_GoogleFT_SearchTerms::getSearchTerms();
                
        foreach( $searchTerms as $term ) {
            $message = self::scrubMessage($message, $term);
        }
        return $message;
    }
    
    /**
     *
     * @param string $word the word to scrub from message
     */
    protected static function scrubMessage( $message, $word ) {
        $matches = null;
        
        if( preg_match( "/(.*)$word(.*)/i", $message, $matches ) ) {
            $message = trim( $matches[1] . $matches[2] );
        }
        $message = trim( preg_replace( "/\s+/", " ", $message ) );
        
        return $message;
    }
    
    /**
     * searches message for open term and returns true if found
     * @param string $message
     * @return bool true on open
     */
    protected static function testIsOpen( $message ) {
        if( preg_match( '/open/i', $message ) ) {
            return true;
        }
        return false;
    }
    
    public static function isHelpRequest( $message ) {
        return( strtolower( $message ) == 'help' );
    }
    
    /**
     * checks whether a message is a zip code
     * @param string $message
     * @return boolean true if zip code, else false 
     */
    public static function isZipCode( $message ) {
        
        if( preg_match( '/^\d\d\d\d\d$/', $message ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function isNextCenterRequest( $message ) {
        $result = preg_match( '/^(\d+)$/', $message, $matches );
        return ( $result > 0 );
    }
}
?>

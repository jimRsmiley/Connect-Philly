<?php

/**
 * Description of Request
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_Request_Smsified 
                extends Zend_Controller_Request_Http {
    
    protected $inboundMessage = null;    
    protected $messageType = null;
    protected $userAddress = null;
    protected $time = null;
    

    
    public function getTestTime() {
        if( $this->time == null ) {
            $this->time = time();
        }
        
        return $this->time;
    }
    
    public function setTestTime($time) {
        $this->time = $time;
    }
    
    public function getType() {
        
        if( $this->messageType == null ) {
            
            if( $this->isHelpRequest() ) {
                $this->messageType = Sms_Model_RequestType::$HELP;
            }
            else if( self::isNextCenterRequest($this->getMessage()) ) {
                $this->messageType = Sms_Model_RequestType::$NEXTCENTER;
            }
            else {
                $this->messageType = Sms_Model_RequestType::$ADDRESS;
            }
        }
        
        return $this->messageType;
    }
    
    public function getUserAddress() {
        
        if( $this->userAddress == null ) {
            $message = $this->getMessage();
            $message = self::scrubMessage($message, 'open');
            $searchTerms = self::getSearchTerms( $message );
            $this->setUserAddress( new Connect_UserAddress(
                    array( 'address' => self::scrubOfSearchTerms($message) ) ) );
        }
        
        return $this->userAddress;
    }
    
    /**
     * searches the message for valid search terms
     * @param string $message
     * @return array the array of terms that were matched
     */
    public function getSearchTerms() {
        $message = $this->getMessage();
        
        $matchedTerms = array();
        
        $words = explode( " ", $message );
        
        foreach( $words as $word ) {
            if( Connect_GoogleFT_SearchTerms::isSearchTerm($word) ) {
                array_push( $matchedTerms, $word );
            }
        }
        
        return $matchedTerms;
    }
    
    public function isHelpRequest() {
        $message = $this->getInboundMessage()->getMessage();
        
        return( strtolower( $message ) == 'help' );
    }
    
    public function getMessage() {
        return trim( $this->getInboundMessage()->getMessage() );
    }
    
    public function getInboundMessage() {
        
        if( $this->inboundMessage == null ) 
        {
            $this->inboundMessage = 
                    new Connect_SMS_InboundMessage( $this->getRawBody() );
        }
        
        return $this->inboundMessage;
    }
    
    /**
     * checks whether a message is a zip code
     * @param string $message
     * @return boolean true if zip code, else false 
     */
    public static function messageIsZipCode( $message ) {
        
        if( preg_match( '/^\d\d\d\d\d$/', $message ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function isNextCenterRequest($message) {
        if( self::messageIsZipCode($message) ) {
            return false;
        } else {
            $result = preg_match( '/^(\d+)$/', $message, $matches );
            return ( $result > 0 );
        }
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
    
    public function setInboundMessage( Connect_SMS_InboundMessage $inboundMessage ) {
        $this->inboundMessage = $inboundMessage;
    }
    
    public function testIsOpen() {
        if( preg_match( '/open/i', $this->getMessage() ) ) {
            return true;
        }
        return false;
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
    
    public function setUserAddress( Connect_UserAddress $userAddress ) {
        
        if( empty( $userAddress ) ) {
            throw new InvalidArgumentException("userAddress may not be null");
        }
        $this->userAddress = $userAddress;
    }

}

?>

<?php

/**
 * Represents a response for when a user sends a bad center request
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_SMS_Response_BadCenterRequest extends Connect_SMS_Response {
    
    protected $_errMsg;
    protected $_request;
    
    public function __construct( Connect_CenterRequest $request ) {
        $this->_request = $request;
    }
    
    public function getMessage() {
        
        $retVal = '';
        
        if( $this->_errMsg ) {
            $retVal .= $e->getMessage() . "\n";
        }
    
        $retVal .= 'Address \''.$this->_request->getAddress1()."' was not understood. Please modify"
            . " your request and try again.  Text 'HELP' for"
            . " further instructions.";
        
        return $retVal;
    }
    
    public function setErrorMessage( $errMsg ) {
        $this->_errMsg = $errMsg;
    }
    
}

?>

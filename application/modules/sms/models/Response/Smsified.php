<?php

/**
 * Description of Response
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_Response_Smsified 
                        extends Zend_Controller_Response_Http {
    
    public function sendResponse() {
        
        if( APPLICATION_ENV == 'production' || $this->forceSmsSend() ) 
        {
            $request = Zend_Controller_Front::getInstance()->getRequest();
        
            $msg = $this->getBody();
            if( count( $this->getException() ) != 0  ) 
            {
                $msg = "An error processing your request"
                        . " has occurred and been logged";
            }
            
            self::send(
                    $request->getInboundMessage()->getDestinationAddress(),
                    $request->getInboundMessage()->getSenderAddress(), 
                    $msg
                    );
        }
        parent::sendResponse();
    }
    
    public function send( $smsifiedNumber,$destination,$message,$notifyUrl = null ) {
        
        if( empty( $destination ) ) {
            throw new Exception( "destination may not be empty" );
        }
        
        
        $config = Zend_Registry::get('configuration');
        
        $sms = new Sms_Model_SMSified(    
                    $config->smsified->user, 
                    $config->smsified->pass
                );
        $resourceReferenceJson = $sms->sendMessage( $smsifiedNumber,
                                    $destination,
                                    $message,
                                    $notifyUrl 
                );
        $this->smsResult = true;
    }
    
    public function forceSmsSend() {
        $forceSmsSend = Zend_Registry::get('configuration')->connect->forceSmsSend;
        return ( $forceSmsSend || ( $forceSmsSend == 1 ) );
    }
}

?>

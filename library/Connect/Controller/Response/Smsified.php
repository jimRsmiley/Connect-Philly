<?php

/**
 * Description of Response
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_Controller_Response_Smsified 
                        extends Zend_Controller_Response_Http {
    
    protected $smsFired = false;
    protected $smsResult = null;
    
    public function sendResponse() {
        parent::sendResponse();
        
        $sendSmsMessages = 
                Zend_Registry::get('configuration')->connect->sendSmsMessages;
        
        if( APPLICATION_ENV == 'production' || $sendSmsMessages == 1 ) {

            $request = Zend_Controller_Front::getInstance()->getRequest();
        
            $msg = "";
            if( count( $this->getException() != 0 ) ) {
                $msg = 
                "An error processing your request has occurred and been logged";
            }
            else {
                $msg = $this->getBody();
            }
            
            self::send(
                    $request->getInboundMessage()->getDestinationAddress(),
                    $request->getInboundMessage()->getSenderAddress(), 
                    $msg
                    );
            
            $this->smsFired = true;
        }
        
    }
    
    public function messageWasTexted() {
        return $this->smsFired;
    }
    
    public function send( $smsifiedNumber,$destination,$message,$notifyUrl = null ) {
        
        if( empty( $destination ) ) {
            throw new Exception( "destination may not be empty" );
        }
        
        
        $config = Zend_Registry::get('configuration');
        
        $sms = new Connect_SMS_SMSified(    
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
    
    public function getSmsResult() {
        return $this->smsResult;
    }
}

?>

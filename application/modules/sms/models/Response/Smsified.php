<?php

/**
 * Collect the resonse information and send it to the browser and try to
 * send it to SMSified.  Send a success or failure email message.
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_Response_Smsified 
                        extends Zend_Controller_Response_Http {
    
    protected $smsException = null;
    
    public function sendResponse() {
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        /*
         * sometimes we're testing the controller in the browser, in this case
         * checking if there's a destination address works so it doesn't try
         * to send the response to smsified
         */
        if( $request->getInboundMessage()->getDestinationAddress() != null ) 
        {
            $request = Zend_Controller_Front::getInstance()->getRequest();
        
            $msg = $this->getBody();
            if( count( $this->getException() ) != 0  ) 
            {
                $msg = "An error processing your request"
                        . " has occurred and been logged";
            }
            
            try {
                $resourceJson = self::sendSms(
                        $request->getInboundMessage()->getDestinationAddress(),
                        $request->getInboundMessage()->getSenderAddress(), 
                        $msg
                        );
                
                Connect_FileLogger::info("smsified send result resource:" . $resourceJson );
                
                $options = $this->smsSuccessOptions(
                        $request->getInboundMessage(),
                        $msg );
                
            } catch( SMSifiedException $e ) {
                $this->smsException = $e;
                
                $options = $this->smsFailOptions(
                        $request->getInboundMessage(),
                        $msg );
            }
         
            Connect_Mail::send($options);
        }
        
        // display the sms response on the browser
        parent::sendResponse();
    }
    
    public function sendSms( $smsifiedNumber,
            $destination,
            $message,
            $notifyUrl = null ) {
        
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
        
        return $resourceReferenceJson;
    }
    
    public function getSmsException() {
        return $this->smsException;
    }
    
   public static function smsSuccessOptions( 
           Connect_SMS_InboundMessage $inboundMessage, 
           $smsText ) {
        $msg = "sender: " . $inboundMessage->getSenderAddress() 
                . " requested: '" . $inboundMessage->getMessage() . "'\n"
                . "\n"
                . "response:\n"
                . "'$smsText'\n"
                . "\n"
                . "content length: " . strlen($smsText);
        
        $options = array();
        $options['subject'] = 'SMS from ' . $inboundMessage->getSenderAddress();
        $options['message'] = $msg;
        $options['toAddress'] = Connect_Mail::getSystemToAddresses();
        
        return $options;
    }
    
    public static function smsFailOptions(
            Connect_SMS_InboundMessage $inboundMessage, 
            $smsText ) {
        $options = self::
                    smsSuccessOptions($inboundMessage, $smsText );
        
        $options['subject'] = 'SMS Send Failure';
        return $options;
    }
}

?>

<?php

/**
 * Description of EmailNotificationOfResponse
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Controller_Plugin_Mailer 
    extends Zend_Controller_Plugin_Abstract
                                   {
    
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if( !$this->shouldSendEmail() ) {
            return;
        }

        $inboundMessage = $this->getRequest()->getInboundMessage();
        $responseMessage = $this->getResponse()->getBody();
        
        // notify system addresses of sms interaction
        $options = Connect_Mail_MessageBuilder::
                smsSuccessOptions($inboundMessage, $responseMessage );
        $result = self::sendEmail( $options, $logger = null, $loggerPrefix = null);
    }
    
    public static function sendEmail( $options ) {
            
        Connect_Mail::send( $options );

        return true;
    }
    
    public static function isWhitelistNumber( $phoneNumber ) {
        $config = Zend_Registry::get("configuration");
        
        $whitelistAddresses = $config->connect->mail->whitelistNumbers->toArray();
        
        foreach( $whitelistAddresses as $wlNum ) {
            if( preg_match( "/$phoneNumber/", $wlNum ) ) {
                return true;
            }
        }
    }
    
    public static function forceSendEmails() {
        $forceSendEmails = Zend_Registry::get('configuration')->connect->forceSmsSend;
        return $forceSendEmails == true;
    }
    
    public function getSenderAddress() {
        
        if( $this->getRequest() == null ||
                !($this->getRequest() instanceof Sms_Model_Request_Smsified) ) { return null; }
        
        return $this->getRequest()
                ->getInboundMessage()
                ->getSenderAddress();
    }
    
    // XXX this should be in a plugin
    public function shouldSendEmail() {
        
        // are we forcing a send on all emails?
        if( $this->forceSendEmails() ) {
            print "returning true because of force send emails\n";
            return true;
        } 
        
        // is the sms sender on the whitelist?
        else if( $this->isWhitelistNumber($this->getSenderAddress() ) )
        {
            return false;
        } 
        
        // don't send if we're not on the production system
        else if( APPLICATION_ENV != 'production' ) {
            return false;
        }
        
        return true;
    }
}
?>

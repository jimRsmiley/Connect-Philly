<?php
/**
 * Easily build the options for the Connect_Mail class to send mail.
 * Configures, subject, message and the to addresses, also may build the message
 * 
 * @author jsmiley
 */
class Connect_Mail_MessageBuilder {
    
    protected static $msgFooter = 'This is an automatically generated message from the Connect Philly System.  Do not reply to this message';

    public static function resendSmsAttemptOptions($attemptNum, $senderAddress, $originalMessage) {
        $msg = "sender: " . $senderAddress
                . " original message: '" . $originalMessage . "'\n"
                . "\n"
                . "attempt number:$attemptNum\n"
                . "\n"
                . "content length: " . strlen($originalMessage);
        
        $options = array();
        $options['subject'] = 'SMS Send Faulure';
        $options['message'] = $msg;
        $options['toAddress'] = Connect_Mail::getSystemToAddresses();
        
        return $options;
    }
    
    public static function smsSuccessOptions( Connect_SMS_InboundMessage $inboundMessage, $smsText ) {
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
    
    public static function addCenterOptions(Connect_ComputerCenter $center ) {

        $msg = '';
        foreach( $center->getOptions() as $key => $value ) {
            $msg .= "$key: $value\n";
        }
        $msg .= self::$msgFooter;
        
        $options = array();
        $options['message']     = $msg;
        $options['subject']     = '\''.$center->getLocationTitle() . '\' added to system';
        $options['toAddress']   = Connect_Mail::getAddCenterAddresses();
        
        return $options;
    }
    
    public static function errorMessageOptions( $message ) {
        $config = Zend_Registry::get( 'configuration' );
        
        $options['subject']         = "Connect Philly Error";
        $options['toAddress']       = Connect_Mail::getSystemToAddresses();
        $options['message']         = $message;
        
        return $options;
    }
    


}

?>

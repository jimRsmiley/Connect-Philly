<?php
/**
 * Easily build the options for the Connect_Mail class to send mail.
 * Configures, subject, message and the to addresses, also may build the message
 * 
 * @author jsmiley
 */
class Connect_Mail_MessageBuilder {
    
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
    
    public static function errorMessageOptions( $message ) {
        $config = Zend_Registry::get( 'configuration' );
        
        $options['subject']         = "Connect Philly Error";
        $options['toAddress']       = Connect_Mail::getSystemToAddresses();
        $options['message']         = $message;
        
        return $options;
    }
    


}

?>

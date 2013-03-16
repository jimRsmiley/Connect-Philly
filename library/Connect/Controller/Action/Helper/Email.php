<?php

/**
 * Description of EmailHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Email extends Zend_Controller_Action_Helper_Abstract {

    protected static $msgFooter = 'This is an automatically generated message from the Connect Philly System.  Do not reply to this message';
    
    public function email() {
        print "emailing";
        exit;
    }
    
    public function smsCenterRequest() {
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
    }
    
    public function addCenter() {
        
    }
}

?>

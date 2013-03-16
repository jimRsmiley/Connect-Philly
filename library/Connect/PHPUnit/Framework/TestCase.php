<?php

/**
 * Description of TestCase
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_PHPUnit_Framework_TestCase 
            extends Zend_Test_PHPUnit_ControllerTestCase {
    
    public static function getInboundMessage( $messageText ) {
        $inboundMessage = new Connect_SMS_InboundMessage();
        $inboundMessage->setMessage($messageText);
        $inboundMessage->setSenderAddress(self::getSenderNumber());
        $inboundMessage->setDestinationAddress( self::getSmsifiedNumber() );
        return $inboundMessage;
    }
    
    public static function getSmsifiedNumber() {
        return Zend_Registry::get('configuration')->smsified->number;
    }
    
    /*
     * get a test number generated for testing purposes
     */
    public static function getSenderNumber() {
        return Zend_Registry::get('configuration')->smsified->senderNumber;
    }
}

?>

<?php

class Sms_SmsifiedController extends Zend_Controller_Action
{
    public function indexAction()
    {
        
    }

    public function createInboundMessageAction() {
        
        $form = new Zend_Form();
        
        $inboundMessage = new Connect_SMS_InboundMessage();

        $inboundMessage->setDestinationAddress( "2152408986");
        $inboundMessage->setSenderAddress("2677389559");
        $inboundMessage->setMessage( 'help' );
        
        $this->view->inboundMessageStr = $inboundMessage->__toString();
    }
}


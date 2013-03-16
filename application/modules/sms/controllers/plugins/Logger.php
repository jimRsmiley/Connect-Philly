<?php
class Sms_Controller_Plugin_Logger extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if( $request instanceof Sms_Model_Request_Smsified ) {
            Connect_FileLogger::info( "requestor='" 
                    . $request->getInboundMessage()->getSenderAddress() 
                    . "' message='" 
                    . $request->getMessage() 
                    . "'"
            );
        }
    }
    
    public function dispatchLoopShutdown() {
        Connect_FileLogger::info( "center request response is : "
                . $this->getResponse()->getBody() );
    }
}
?>

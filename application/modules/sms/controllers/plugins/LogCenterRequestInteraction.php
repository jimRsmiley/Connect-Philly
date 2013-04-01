<?php

/**
 * Description of EmailNotificationOfResponse
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Controller_Plugin_LogCenterRequestInteraction
    extends Zend_Controller_Plugin_Abstract
                                   {
    public function preDispatch( Zend_Controller_Request_Abstract $request ) {
        
        // log only on center-request controller
        if( 'center-request' != $request->getControllerName() ) {
            return;
        }

        Connect_FileLogger::info( "request body: " . $request->getRawBody()  );
    }
    
    public function dispatchLoopShutdown()
    { 
        $request = $this->getRequest();
        
        // log only on center-request controller
        if( 'center-request' != $request->getControllerName() ) {
            return;
        }
        
        Connect_FileLogger::info( "center request response is : "
                . $this->getResponse()->getBody() );
    }
}
?>

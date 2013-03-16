<?php

/**
 * Description of LogInboundMessage
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Controller_Plugin_LogInboundMessage 
    extends Zend_Controller_Action_Helper_Abstract {
    
    public function preDispatch( $request ) {
        
        Connect_FileLogger::info( "logging inboundMessage" );
        Connect_FileLogger::info( Zend_Debug::dump( $request ) );
    }
}

?>

<?php

/**
 * Description of Bootstrap
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin( new Connect_Controller_Plugin_EmailNotificationOfResponse() );
    }
    public function _initRegisterRequest() {
        
        $front = Zend_Controller_Front::getInstance();

        $front->setRequest( 
                new Connect_Controller_Request_Smsified() );
        
        $front->setResponse( 
                new Connect_Controller_Response_Smsified() );
    }
}
?>

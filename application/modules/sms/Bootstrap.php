<?php

/**
 * Description of Bootstrap
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAutoloader()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(  
                                array(  
                                    'namespace' => 'Sms',  
                                    'basePath' => APPLICATION_PATH . '/modules/sms'  
                                )
                            );  
  
        // adding model resources to the autoloader  
        $moduleLoader->addResourceTypes(  
                array(  
                    'plugins' => array(  
                        'path' => 'controllers/plugins',  
                        'namespace' => 'Controller_Plugin'  
                    )  
                )  
            );  
  
        return $moduleLoader;  
    }
    
    public function _initRequestResponseObjects() {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');
        
            $front->setRequest( 
                    
                new Sms_Model_Request_Smsified() );
        
        $front->setResponse( 
                new Sms_Model_Response_Smsified() );
    }
    
    protected function _initPlugins()
    {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');
        
        $front->registerPlugin(
                new Sms_Controller_Plugin_LogCenterRequestInteraction());
    }
}
?>

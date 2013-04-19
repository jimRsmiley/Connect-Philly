<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * we'll avoid all views and layouts and just forward 
     */
    public function indexAction()
    {
        $config = Zend_Registry::get('configuration');
        $forwardUrl = $config->forwardUrl;
        
        $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $r->gotoUrl($forwardUrl)->redirectAndExit();
    }

    /**
     * created just for testing the ErrorController
     * @throws Exception
     *
     */
    public function errorAction()
    {
        throw new Exception("this is a test action created just for testing " 
                                . "the ErrorController" );
    }
}








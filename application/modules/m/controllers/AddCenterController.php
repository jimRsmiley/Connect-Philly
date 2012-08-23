<?php

class M_AddCenterController extends Zend_Controller_Action
{
    public function indexAction() {
        // this is the page that shows the initial form
    }
    public function addAction()
    {
        // is this an ajax request? if so, the parameters are in a json string
        // in the request body.
        // using jquery so this is ok
        if($this->getRequest()->isXmlHttpRequest()) 
        {
            $json = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($json);
        }
        
        $computerCenter = new Connect_ComputerCenter($params);
        $centerMapper = new Connect_ComputerCenterMapper();
        $centerMapper->save($computerCenter);
    }
}




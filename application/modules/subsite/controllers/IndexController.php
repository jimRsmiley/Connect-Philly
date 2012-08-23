<?php

class Subsite_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {   
        $form = new Subsite_Form_MapSearch();
        //$form->setAction(null);
        $form->setMethod('GET');
        $this->view->searchForm = $form;
    }

    public function centerbyrowidAction()
    {    
        $rowid = $this->getRequest()->getParam('rowid');
        
        $centerMapper = new Connect_ComputerCenterMapper();
        $data = $centerMapper->getCenterByRowid($rowid);
        print_r($data);
        
        echo Zend_Json::encode($data->getOptions(),0);
        $this->_helper->json($data);
    }

    /**
     * created just for testing the ErrorController
     * @throws Exception
     */
    public function errorAction() {
        throw new Exception("this is a test action created just for testing the ErrorController" );
    }
}




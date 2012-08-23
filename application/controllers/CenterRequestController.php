<?php

class CenterRequestController extends Zend_Controller_Action
{

    public function init()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext( 'by-Location','json');
        $contextSwitch->addActionContext( 'by-location','json');
        $contextSwitch->addActionContext( 'by-rowid', 'json' );
        $contextSwitch->addActionContext( 'by-Rowid', 'json' );
        $contextSwitch->setAutoJsonSerialization(false);
        $contextSwitch->initContext();
    }

    public function byRowidAction()
    {
        $rowid = $this->getRequest()->getParam('rowid');
        
        $centerMapper = new Connect_ComputerCenterMapper();
        $center = $centerMapper->getCenterByRowid($rowid);
        
        $this->view->center = $center;
        $this->view->timestamp = time();
    }

    public function byLocationAction()
    {
        // is this an ajax request? if so, the parameters are in a json string
        // in the request body.
        // using jquery so this is ok
        if($this->getRequest()->isXmlHttpRequest()) 
        {
            $json = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($json);
        }
        
        // else just pull the parameters from the get/post request
        else 
        {
            $params = $this->getRequest()->getParams();
        }
        
        $lat = $params['lat'];
        $lng = $params['lng'];
        
        if( array_key_exists('numCenters',$params) )
            $numCenters = $params['numCenters'];
        else
            $numCenters = null;
        
        $searchOptions = array();
        if( array_key_exists('searchOptions',$params ) )
            $searchOptions  = $params['searchOptions'];
        
        $position = new Connect_Position( $lat, $lng );
        $centerMapper = new Connect_ComputerCenterMapper();
        $centers = $centerMapper->getCenters(
                $position,
                $searchOptions,
                $numCenters
                );
        
        $this->view->centers = $centers;
        $this->view->timestamp = time();
    } // end byLocationAction
}
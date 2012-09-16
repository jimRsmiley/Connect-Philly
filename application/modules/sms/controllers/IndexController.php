<?php

class Sms_IndexController extends Zend_Controller_Action
{
    public function indexAction() {}
    
    public function nearestCenterAction() {
        
        $request = $this->getRequest();
        
        $requestType = $request->getType();

        switch( $requestType ) {
            
            case Connect_Controller_Request_Type::$HELP:
                
                $this->_helper->viewRenderer->setRender( 'help' );
                break;
            
            case Connect_Controller_Request_Type::$ADDRESS:
            {
                try {
                    $mapper = new Sms_Model_SmsifiedComputerCenterMapper();

                    $testTime = null;
                    if( $request->testIsOpen() ) {
                        $testTime = $request->getTime();
                    }
                    
                    $center = $mapper->getCenter( 
                                $request->getAddress(), 
                                $request->getSearchTerms(),
                                $nextCenterNum = 0,
                                $testTime
                        );

                    $this->view->center = $center;
                    $this->view->nextCenterNum = 1;
                    $this->view->testTime = time();
                    $this->_helper->viewRenderer->setRender( 'nearest-center' );
                    
                    /*
                     *  only time you store the address
                     */
                    Connect_SMS_PastMessageFile::store( 
                            $this->getRequest()->getInboundMessage() );
                    
                    /*
                     * now store the usage data
                     */
                    //self::storeUsageData(
                    //        $request, $this->getRequest()->getInboundMessage()->getSenderAddress() );
                }
                catch( Sms_Model_Exception_BadAddress $ex ) {
                    $this->view->message = $this->getRequest()->getMessage();
                    $this->_helper->viewRenderer->setRender( 'not-understood' );
                }
            } 
            break;
        
            case Connect_Controller_Request_Type::$NEXTCENTER:
            {
                $mapper = new Sms_Model_SmsifiedComputerCenterMapper();
            
                $center = $mapper->nextCenter( 
                            $request->getMessage(), 
                            $request->getInboundMessage()->getSenderAddress() 
                    );
    
                if( $center == null ) {
                    $this->_helper->viewRenderer->setRender( 'no-next-center' );
                }
                else {
                    $this->view->center = $center;
                    $this->view->nextCenterNum = $request->getMessage()+1;
                    $this->view->testTime = time();
                    $this->_helper->viewRenderer->setRender( 'nearest-center' );
                    }
            }
            break;
        } // end switch
            
        // after switch statement, controller will dispatch
    
    } // end nearestCenterAction
    
    public static function storeUsageData(Connect_CenterRequest $request, $senderAddress ) {
        
        if( !preg_match( "/2677389559/", $senderAddress ) 
                && APPLICATION_ENV == 'production' ) 
        {
            $properties = array( 
                'timestamp' => Connect_AbstractMapper::getFusionTableTimestamp( time() ),
                'centerRequestText' => $request->getAddress1(),
                'latitude'          => $request->getLatitude(),
                'longitude'         => $request->getLongitude()
            );
            $usageData = new Connect_UsageData($properties);

            $mapper = new Connect_UsageDataMapper();
            $mapper->save( $usageData );
        }
    }
}

<?php

class Sms_CenterRequestController extends Zend_Controller_Action
{   
    public function nearestCenterAction() {

        $request = $this->getRequest();
                        
        switch( $request->getType() ) {
            
            case Sms_Model_RequestType::$HELP:
            {
                $this->_helper->viewRenderer->setRender( 'help' );
            }
            break;
            
            case Sms_Model_RequestType::$ADDRESS:
            {
                $this->processAddressRequest();
            } 
            break;
        
            case Sms_Model_RequestType::$NEXTCENTER:
            {
                $this->processNextCenterRequest();
            }
            break;
        } // end switch
            
        // after switch statement, controller will dispatch
    
    } // end nearestCenterAction
    
    public function processNextCenterRequest() {
        $request = $this->getRequest();
        
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
            $this->view->testTime = $request->getTestTime();
            $this->_helper->viewRenderer->setRender( 'nearest-center' );
            }
    }
    public function processAddressRequest() {
        $request = $this->getRequest();
        
        $mapper = new Sms_Model_SmsifiedComputerCenterMapper();

        $testTime = null;
        if( $request->testIsOpen() ) {
            $testTime = $request->getTestTime();
        }
        
        try {
            $center = $mapper->getCenter( 
                        $request->getAddress(), 
                        $request->getSearchTerms(),
                        $nextCenterNum = 0,
                        $testTime
                );

            $this->view->center = $center;
            $this->view->nextCenterNum = 1;
            $this->view->testTime = $request->getTestTime();
            $this->_helper->viewRenderer->setRender( 'nearest-center' );
        }
        catch( Sms_Model_Exception_BadAddress $ex ) {
            $this->view->message = $this->getRequest()->getMessage();
            $this->_helper->viewRenderer->setRender( 'not-understood' );
        }
    }

    
    /**
     * create the nofity url for callback url
     * @return string the callback url
     */
    public static function getNotifyUrl($serverName,$attemptNum) {
        $url = "http://$serverName/sms/index/smsified_callback.php?attemptNum=$attemptNum";
        $url = urlencode( $url );
        return $url;
    }
    
    public function callbackAction()
    {
        $notification = new Connect_SMS_DeliveryInfoNotification($json);

        if( empty($attemptNum) ) {
            throw new Connect_Exception( 'attemptNum cannot be empty' );
        }
        
        if( $notification->getDeliveryStatus() == 'DeliveredToNetwork' ) {}
        
        // if it failed and there was only one attempt to deliver
        else if( $notification->getDeliveryStatus() !=  'DeliveredToNetwork'
                && $attemptNum == '1' ) {
            
            $logger->info( $loggerPrefix.' previous sms failed.' );
            
            $logger->info( $loggerPrefix.print_r( $notification, true ) );
            
            /*
             * @todo do something here if you actually want to start resending SMS messages
             * 
            $notifyUrl = self::getNotifyUrl($_SERVER['SERVER_NAME'], ++$attemptNum);
            
            $senderAddress = $notification->getSenderAddress();
            $toAddress = $notification->getAddress();
            try {
                $result = self::smsSend(
                            $senderAddress, 
                            $toAddress, 
                            $notification->getMessage(), $notifyUrl 
                        );
                $logger->debug( $loggerPrefix.'smsSend succeeded' );
            }
            catch( SMSifiedException $e ) {
                $logger->error( $e->getMessage() );
            }
             */
            
            
            // notify systems admin via email
            $options = Connect_Mail_MessageBuilder::resendSmsAttemptOptions(
                            $attemptNum-1, 
                            $toAddress, 
                            $notification->getMessage() 
                    );
            self::sendEmail( $options, $logger, $loggerPrefix );
        }
        return true;
    }
}




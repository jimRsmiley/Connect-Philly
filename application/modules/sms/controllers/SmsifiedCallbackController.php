<?php

/**
 * Description of SmsifiedCallbackController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class SmsifiedCallbackController {
    
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

?>

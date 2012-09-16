<?php

/**
 * Description of EmailNotificationOfResponse
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_Controller_Plugin_EmailNotificationOfResponse 
                                    extends Zend_Controller_Plugin_Abstract{
    
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        if( APPLICATION_ENV != 'production' ) {
            return;
        }
        
        $response = $this->getResponse();
       

        $inboundMessage = $this->getRequest()->getInboundMessage();
        $responseMessage = $this->getResponse()->getBody();
        
        // notify system addresses of sms interaction
        $options = Connect_Mail_MessageBuilder::
                smsSuccessOptions($inboundMessage, $responseMessage );
        $result = self::sendEmail( $options, $logger = null, $loggerPrefix = null);
    }
    
    public static function sendEmail( $options, $logger = null, $loggerPrefix = null ) {
            
        if( !empty( $logger ) ) {
            $logger->info( $loggerPrefix.str_replace( "\n", '; ', $options['message'] ) );
        }
        Connect_Mail::send( $options );

        return true;
    }
}

?>

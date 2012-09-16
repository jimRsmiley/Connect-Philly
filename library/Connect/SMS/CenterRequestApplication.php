<?php

/**
 * Handles requests for centers
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_SMS_CenterRequestApplication extends Connect_SMS_Application {
    
    /**
     * process the request json and sms a response, also returns the response
     * message or null on failure
     * 
     * @param type $json
     * @return boolean true on successful sms response, false on sms api send
     * failure
     */
    public static function run($json) {
        
        // @todo figure out how to do without this
        // needed because of notify url
        if( empty($_SERVER['SERVER_NAME'] ) ) {
            $_SERVER['SERVER_NAME'] = 'connect.jimsmiley.us';
        }
        
        $config = Zend_Registry::get('configuration');
        $logger = Zend_Registry::get('Log');
        $loggerPrefix = __CLASS__ . '->' . __FUNCTION__ . ": ";
        
        $logger->crit( 'Application->run '. $json );
        $inboundMessage = new Connect_SMS_InboundMessage($json);
        $logger->info( $loggerPrefix.'********* received center request \''
                . $inboundMessage->getMessage() 
                . '\' **********' );
        

        $responseMessage = self::processInboundMessage($inboundMessage);
        
        $attemptNum = '1';
        $notifyUrl = self::getNotifyUrl($_SERVER['SERVER_NAME'], $attemptNum );
        $logger->info( $loggerPrefix. 'using notifyUrl ' 
                . urldecode( $notifyUrl ) );
        /*
         * try and reply to the incoming sms message through the SMSified
         * API
         */
        try {
            
            self::smsSend(  $inboundMessage->getDestinationAddress(), 
                            $inboundMessage->getSenderAddress(), 
                            $responseMessage,
                            $notifyUrl
                    );
            
            
            // success!

            
            // notify system addresses of sms interaction
            $options = Connect_Mail_MessageBuilder::smsSuccessOptions($inboundMessage, $responseMessage );
            $smsResult = self::sendEmail( $options, $logger, $loggerPrefix );
            $logger->debug( "resendSMS result '$smsResult'" );
        }
        
        /*
         * if replying to the message fails, send an email to Connect Philly admin
         */
        catch (SMSifiedException $ex) {
            $config = Zend_Registry::get('configuration');
            
            $responseText = 'attempt to send SMS message to ' 
                    . $inboundMessage->getSenderAddress() . ' failed';
            $logger->warn( $responseText );
            $logger->err(  $ex->getMessage() );

            $mailOptions = array();
            $mailOptions['subject'] = 'SMS Error';
            $mailOptions['message'] = $responseText . "\n" . $ex->getMessage();
            $mailOptions['toAddress'] = 
                        $config->mail->systemMessages->toAddresses->toArray();

            Connect_Mail::send( $mailOptions );
        }
        
        return $responseMessage;
    }
    
    /**
     * process the inboundMessage and return the message to send back to the user.
     * store the message for future use if required
     * 
     * @param Connect_SMS_InboundMessage $inboundMessage
     * @return string the message 
     */
    public static function processInboundMessage( 
                            Connect_SMS_InboundMessage $inboundMessage ) {
        
        $request = Connect_SMS_RequestBuilder::create( $inboundMessage );
        
        $response = Connect_SMS_ResponseBuilder::create($request);
        
        // this is a first address request, store the message for later
        if( Connect_SMS_PastMessageFile::shouldStoreRequest($request, $response) ) {
            Connect_SMS_PastMessageFile::store($inboundMessage);
            self::storeUsageData($request, $inboundMessage->getSenderAddress() );
            

        }
        
        return $response->getMessage();
    }
    
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
    
    /**
     * create the nofity url for callback url
     * @return string the callback url
     */
    public static function getNotifyUrl($serverName,$attemptNum) {
        $url = "http://$serverName/sms/smsified_callback.php?attemptNum=$attemptNum";
        $url = urlencode( $url );
        return $url;
    }
}

?>

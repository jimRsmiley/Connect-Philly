<?php

/**
 * The main sms application.  It processes the inbound message.  Primarily it
 * runs the the request and response builders and makes handles failures in
 * sending response SMS.
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_SMS_Application {
    
    public static function smsSend($destination,$sender,$message,$notifyUrl = null ) {
        $config = Zend_Registry::get('configuration');
        $sms = new Connect_SMS_SMSified( $config->smsified->user, $config->smsified->pass );
        $result = $sms->sendMessage( $destination,$sender,$message,$notifyUrl );
        return $result;
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

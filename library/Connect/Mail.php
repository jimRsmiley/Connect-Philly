<?php

/**
 * Connect_Mail extends JS_Mail to allow easier access to sending messages.
 * JS_Connect_Mail knows how to obtain the smtp username, password, and the senders
 * addresses for exceptions and notifications that sms was interacted with
 * 
 * Connect_Mail knows who to send mail to in different situations
 *
 * @author JimS
 */
class Connect_Mail  {
    
    protected static $mailerName = 'Connect Philly Mailer';
    protected static $smtpHost = 'smtp.gmail.com';
    
    public static function send( $options ) {

        if( empty( $options['message'] ) ) {
            throw new Connect_Exception( 'message option must be defined' );
        } else if( empty( $options['subject'] ) ) {
            throw new Connect_Exception('subject option must be defined' );
        } else if( empty( $options['toAddress'] ) ) {
            throw new Connect_Exception('toAddress option must be defined' );
        }
        
        $config = Zend_Registry::get( 'configuration' );
        
        $options['senderAddress']   = $config->google->user;
        $options['smtpPass']        = $config->google->pass;
        $options['smtpHost']        = self::$smtpHost;
        $options['senderName']      = self::$mailerName;
        
        self::smtpCall( $options );
    }
    
    protected static function smtpCall( $options ) {
        
        $smtpConf = array(
                        'auth' => 'login',
                        'ssl' => 'ssl',
                        'port' => '465',
                        'username' => $options['senderAddress'],
                        'password' => $options['smtpPass']
                    );

        try 
        {
            $transport = new Zend_Mail_Transport_Smtp($options['smtpHost'], $smtpConf);

            $mail = new Zend_Mail();
            $mail->setFrom($options['senderAddress'], $options['senderName'] );
            $mail->addTo( $options['toAddress'] );
            $mail->setSubject($options['subject']);
            $mail->setBodyText($options['message']);
            
            if( true ) {
                Connect_FileLogger::info( "sending mail" );
                Connect_FileLogger::info( "senderAddress: " . $options['senderAddress'] );
                Connect_FileLogger::info( "senderName: " . $options['senderName'] );
                Connect_FileLogger::info( "toAddress: " . $options['toAddress'] );
                Connect_FileLogger::info( "subject: " . $options['subject'] );
                Connect_FileLogger::info( "message: " . $options['message'] );
            }
            
            
            try {
                $mail->send($transport);
            }
            catch( Exception $e ) {
                $errMsg = "error sending mail: '".$e->getMessage();
                Connect_FileLogger::error( $errMsg );
                return false;
            }
            return true;
        }
        catch( Zend_Mail_Protocol_Exception $e ) {
            $errMsg = "error sending mail with content '".$options['message'].".'; reason: ".$e->getMessage();
            Connect_FileLogger::error( $errMsg );
            return false;
        }
    }
    
    public static function getSystemToAddresses() {
        $config = Zend_Registry::get('configuration');
        return $config->mail->systemMessages->toAddresses->toArray();
    }
    
    
    public static function getAddCenterAddresses() {
        $config = Zend_Registry::get('configuration');
        return $config->mail->addCenter->toAddresses->toArray();
    }
}
?>

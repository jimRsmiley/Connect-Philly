<?php

/**
 * Description of InboundMessageType
 * @todo decide if this class should exist
 * @author JimS
 */
class Connect_SMS_InboundMessageType {
    
    public static function isHelp( $message ) {
        return (strcmp( strtolower($message), "help" ) == 0);
    }
    
    public static function isNextCenterRequest( $message ) {
        return preg_match( '/^\d+$/', $message );
    }
}

?>

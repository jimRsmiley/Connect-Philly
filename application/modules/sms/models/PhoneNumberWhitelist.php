<?php

/**
 * a collection of phone numbers that are not to be recorded for various reasons,
 * like not recording usage data for
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_PhoneNumberWhitelist {
    
    public static function exists( Connect_TelephoneNumber $number ) {
        
        $config = Zend_Registry::get('configuration');
        
        $whitelistNumbers = $config->connect->whitelistNumbers->toArray();
        
        foreach( $whitelistNumbers as $number ) {
            $telNum = new Connect_TelephoneNumber($number);
            
            if( $telNum->equals( $telNum ) ) {
                return true;
            }
        }
        
        return false;
    }
}

?>

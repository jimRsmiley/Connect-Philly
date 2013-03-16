<?php

/**
 * given a computer center and timestamp, formats an appropriate open status
 * string for display
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_View_Helper_OpenNowString extends Zend_View_Helper_Abstract {
    
    //public function __construct() {}
    
    public static function openNowString(
                        Connect_ComputerCenter $center, $timestamp ) {
       
        $openstatus = $center->getOpenStatus( $timestamp );
        
        if( $openstatus == Connect_ComputerCenter_OpenStatus::$OPEN ) {
            return "Open Now";
        }
        
        // currently closed
        else if( $openstatus == Connect_ComputerCenter_OpenStatus::$CLOSED ) {
            
            $day = self::getDayFromTimestamp($timestamp);
            
            $openCloseTimes = $center->getOpenCloseTimes($day);
            $openTime = $openCloseTimes[0];
            $opentime = date( "g:i A");
            
            // need to cat on the day
            $openTimestamp = strtotime( $day . ' ' . $openTime );
            
            // if the center hasn't opened yet today but will
            if( self::lessThan( $timestamp,$openTimestamp ) ) {
                return 'Opens at ' . $openTime;
            }
            // else
            else {
                return 'Closed Now';
            }
        }
        
        return "";
    }
    
    public static function getDayFromTimestamp($timestamp) {
        return date('l',$timestamp);
    }
    
    /**
     * tests whether the first timestamp is less than the second timestamp
     * @param type $t1
     * @param type $t2 
     * @return bool true or false
     */
    public static function lessThan( $t1, $t2 ) {
        return $t1 < $t2;
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}

?>

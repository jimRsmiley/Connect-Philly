<?php

/**
 * Description of AddCenterMailer
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_Mail_AddCenterMailer extends Connect_Mail {
    
    public function mail( Connect_ComputerCenter $center ) {
    
        $msg = '';
        foreach( $center->getOptions() as $key => $value ) {
            $msg .= "$key: $value\n";
        }
        $msg .= self::$msgFooter;
        
        $options = array();
        $options['message']     = $msg;
        $options['subject']     = '\''.$center->getLocationTitle() . '\' added to system';
        $options['toAddress']   = Connect_Mail::getAddCenterAddresses();
        
        parent::send($options);
    }
}
?>

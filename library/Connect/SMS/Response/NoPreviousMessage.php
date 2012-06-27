<?php

/**
 * The message to send back to the user when they've sent a probably next
 * center request consisting of all digits, but they haven't previously texted
 * a valid address.
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NoPreviousMessage {
    
    /**
     * returns the message associated with thfe response when an sms message
     * contains only numbers but no previous message had been sent.  So no other
     * lookup could be done.
     * @return string the message
     */
    public function noPreviousMessageFound() {
        $msg = 'No previous address found.  Please text an address '
                . ' before trying to find the next nearest center.';
        return $msg;
    }
}

?>

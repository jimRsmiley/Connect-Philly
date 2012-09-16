<?php

/**
 * Description of FusionTableException
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_GoogleFT_FusionTableException extends Exception {
    
    public function __construct($result) {
        
        if( preg_match( "/<title>(.*)<\/title>/i", $result, $matches ) ) {
            $this->message = $matches[1];
        }
        else {
            throw new Exception( 'unable to understand response: ' . $result );
        }
    }
}

?>

<?php

/**
 * Description of FusionTableException
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class CenterTableException extends Exception {
    
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

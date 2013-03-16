<?php

/**
 * Description of TelephoneNumber
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_TelephoneNumber {
    
    protected $digits = null;
    
    public function __construct( $string ) {
        
        if( empty( $string ) ) {
            throw new InvalidArgumentException( "string may not be empty" );
        }
        $this->digits = $this->getDigits($string);
    }
    
    public function __toString() {
        return "tel:+".$this->digits;
    }
    
    public static function getDigits( $string ) {
        preg_match( "/\d+/", $string, $matches );
        return $matches[0];
    }
}

?>

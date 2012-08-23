<?php

/**
 * create a url given an address
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Zend_View_Helper_GoogleMapsUrl extends Zend_View_Helper_Abstract
{   
    public static function googleMapsUrl( $address ) {
        
        return 'http://maps.google.com/maps?q='
                    .urlencode($address);
    }
}

?>

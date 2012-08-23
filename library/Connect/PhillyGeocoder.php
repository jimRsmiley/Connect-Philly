<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JS_Philly_Geocoder
 *
 * @author JimS
 */
class Connect_PhillyGeocoder extends Connect_GISGeocoder {
    
    public static $PHL_LNG = '-75.163789';
    public static $PHL_LAT = '39.952335';
    
    public static function geocode($address) {
        
        if( empty( $address ) ) {
            throw new InvalidArgumentException('address may not be empty');
        }
        
        $address = $address . ', Philadelphia, PA';
                
        $position = parent::geocode( $address );
        
        // reset the result if it returns the general philly location,
        // we want to return null
        if( self::isPhillyCoords($position) ) {
            $position = null;
        }
        
        return $position;
        
    }
    
    /**
     *tests if the position supplied is the philadelphia coordinates returned
     * by google, this indicates that the geocoding a particular address failed
     * @param Connect_Position $position the position to test
     */
    public static function isPhillyCoords( Connect_Position $position ) {
        return $position->getLat() == self::$PHL_LAT 
                && $position->getLng() == self::$PHL_LNG;
    }
}

?>

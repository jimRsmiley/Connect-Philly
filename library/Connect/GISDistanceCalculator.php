<?php

class Connect_GISDistanceCalculator
{
    /**
     * pilfered from http://www.zipcodeworld.com/samples/distance.php.html
     * 
     * @param type $latitude1
     * @param type $longitude1
     * @param type $latitude2
     * @param type $longitude2
     * @param type $unit
     * @return type 
     */
    public static function distance( $latitude1, $longitude1, 
                                $latitude2, $longitude2, 
                                $unit = 'M' ) {

        $theta = $longitude1 - $longitude2; 
        $dist = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)); 
        $dist = acos($dist); 
        $dist = rad2deg($dist); 
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ( strtoupper($unit) == "K") {
            return ($miles * 1.609344); 
        } else if ( strtoupper($unit) == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}


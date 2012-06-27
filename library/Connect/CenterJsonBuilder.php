<?php

/**
 * handles the building of json strings for computer centers
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_CenterJsonBuilder {
    
    public static function create( Connect_ComputerCenter $center, $precision = 2 ) {
        
        if( empty( $center ) ) {
            throw new InvalidArgumentException('center cannot be empty');
        }
        
        $options = $center->toArray();
        
        /*
        $options = array( 
                'locationTitle' => $center->getLocationTitle(),
                'latitude'           => $center->getLatitude(),
                'longitude'           => $center->getLongitude(),
                'address1'      => $center->getAddress1(),
                'centerPhoneNumber'      => $center->getCenterPhoneNumber()
            );
        */
        if( $center instanceof Connect_FoundCenter ) {
            $options['distance'] = round( $center->getDistanceFromRequest(), $precision );
        }
        
        $json = json_encode( $options );
        return $json;
    }
}

?>

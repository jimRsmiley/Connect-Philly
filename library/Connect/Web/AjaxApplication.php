<?php

/**
 * Description of AjaxApplication
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_Web_AjaxApplication {
    
    /**
     * given a json string that should be a center request, return a json
     * string of all the centers that correspond to that request
     * 
     * @param string $jsondiv
     * @return string json string of computer centers
     */
    public static function run( $json, $limit = 10 ) {
    
        $request = self::buildCenterRequest($json);
        
        $centers = Connect_ComputerCenterMapper::getCenters($request, $limit);
        
        return self::getJson($centers);
    }
    
    /**
     * given a json string that should be a center request, return the request
     * object
     * @param type $json
     * @return \Connect_CenterRequest 
     */
    public static function buildCenterRequest( $json ) {
        
        $array = json_decode($json, true);
        
        $request = new Connect_CenterRequest( $array );
        
        return $request;
    }
    
    /**
     * given an array of computer centers, returns them in json form
     * @param array $centers an array of computer center objects
     */
    public static function getJson( $centers ) {
        
        if( !is_array( $centers ) ) {
            throw new InvalidArgumentException('$centers must be type array');
        }
        
        $json = '{"ComputerCenters":[';
        
        foreach( $centers as $center ) {
            $json .= Connect_CenterJsonBuilder::create( $center ) . ',';
        }
        
        // get rid of that last comma
        $json = substr( $json, 0, strlen($json) - 1 );
        
        $json .= ']}';
        
        return $json;
    }
}

?>

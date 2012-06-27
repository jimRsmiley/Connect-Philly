<?php

/**
 * Description of CenterRequestApplication
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_CenterRequestApplication {
    
    public static function run( $json ) {
        
        $options = json_decode( $json );
        $request = new Connect_CenterRequest( $options );
        
        return self::processCenterRequest($request);
        
    }
    
    public static function processCenterRequest( Connect_CenterRequest $request ) {
        
        $response = Connect_ComputerCenterMapper::create($request);
        
        return $response->getFoundCenter();
    }
}

?>

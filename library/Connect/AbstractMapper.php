<?php

/**
 * Description of AbstractMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_AbstractMapper {
    
    /*
     * Connect fusion table columns have each word capitalized and spaces
     * between them.  PHP objects are camel cased.  This function takes a
     * php object and returns an assiative array with the column names as
     * keys.
     */
    public function getInsertData( $object ) {
        $data = array();
        
        foreach ($object->getProperties() as $key => $value ) {
                
            if( isset( $value ) && !preg_match( "/^\s*$/", $value ) ) {

                $method = 'get'.$key;
                // add a space in front of the capital letters
                $key = preg_replace('/(?<!\ )[A-Z]/', ' $0', $key);
                $key = ucfirst( $key );

                $data[$key] = addslashes( $object->$method() );
            }
        }
        
        return $data;
    }
    
    /**
     * given a unix timestamp, returns the same kind of string used in the
     * fusion table date column formatted like "8/24/12 9:30 PM"
     * @param string $phpTimestamp
     * @return string
     */
    public static function getFusionTableTimestamp( $phpTimestamp ) {
        return date( "n/d/y g:i A", $phpTimestamp );
    }
}

?>

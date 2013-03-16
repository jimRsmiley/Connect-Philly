<?php

/**
 * Description of ComputerCentersJson
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_View_Helper_ComputerCentersJson extends Zend_View_Helper_Abstract
{
    protected static $jsonArrayName = 'ComputerCenters';
    protected static $distanceFromPositionPrecision = 2;
    
    public function computerCentersJson( $val, $timestamp = null ) {
        
        // if the value is empty
        if( empty($val) ) {
            return "[]";
        }

        
        $json = '{"' . self::$jsonArrayName . '":[';
        
        // if it's one computer center
        if( !is_array( $val ) ) {
            $json .= self::create( $val, $timestamp );
        }

        
        else { // if it is an array
            foreach( $val as $center ) {
                $json .= self::create( $center, $timestamp ) . ',';
            }

            // get rid of that last comma
            $json = substr( $json, 0, strlen($json) - 1 );
        }
        
        $json .= ']}';

        return $json;
    }
    
    public function create( Connect_ComputerCenter $center, $timestamp ) {
        
        if( empty( $center ) ) {
            throw new InvalidArgumentException('center cannot be empty');
        }
        
        $options = $center->toArray();
        
        /*
         * tack on the distance
         */
        if( array_key_exists('distanceFromPosition',$options) ) {
            $options['distanceFromPosition'] = 
                round( $options['distanceFromPosition'], self::$distanceFromPositionPrecision );
        }
        
        
        /*
         * do the open string
         */
        if( !empty($timestamp) ) {
            $options['openNowString'] = 
                    Connect_View_Helper_OpenNowString::openNowString( $center, $timestamp );
        }
        
        $json = json_encode( $options );
        
        return $json;
    }
    
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}

?>

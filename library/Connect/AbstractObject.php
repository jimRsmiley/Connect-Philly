<?php

/**
 * Notes: subclasses should name their variables $name, not $_name
 * and must implement the getClassVariables() function to return an array
 * of thier variable names
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_AbstractObject {
    
    public function __construct( $properties = null ) {
        if (is_array($properties)) {
            $this->setProperties($properties);
        }
    }
    
    public function setProperties(array $props)
    {
        $methods = get_class_methods($this);
        
        foreach ($props as $key => $value) {
            $method = 'set' . ucfirst($key);
            $method = str_replace( ' ', '', $method );
            
            // see if this is a valid method
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getProperties() {
        
        $variables = $this->getClassVariableNames();
 
        $options = array();
        
        foreach( $variables as $variableName ) {
            
            $method = 'get' . ucfirst( $variableName );
            
            //print( "variableName $variableName method-name $method\n" );
            if( method_exists( $this, $method ) ) {
                $options[$variableName] = $this->$method();
            }
        }
        
        return $options;
    }
}

?>

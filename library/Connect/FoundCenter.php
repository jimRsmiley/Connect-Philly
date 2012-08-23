 <?php

/**
 * A computer center that was created as a result of a search in the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_FoundCenter extends Connect_ComputerCenter {
    
    // the center submitted to search
    protected $_searchPosition;
    protected $_distanceFromPosition;
     
    public function getDistanceFromPosition() {
        
        if( $this->_distanceFromPosition == null ) {
            $this->_distanceFromPosition = Connect_GISDistanceCalculator::distance(
                $this->getLatitude(), $this->getLongitude(),
                $this->_searchPosition->getLat(), $this->_searchPosition->getLng()
                 );
        }
        return $this->_distanceFromPosition;
    }
    
	public function getSearchPosition(){
		return $this->_searchPosition;
	}

	public function setSearchPosition( Connect_Position $searchPosition){
		$this->_searchPosition = $searchPosition;
        $this->_distanceFromPosition = self::getDistanceFromPosition();
	}
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            $method = str_replace( ' ', '', $method );
            
            // see if this is a valid method
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getOptions() {
        
        //$variables = array_keys( get_class_vars( get_class(new Connect_ComputerCenter) ) );
        $variables = array_keys( get_class_vars( __CLASS__ ) );
        $options = array();
        foreach( $variables as $variableName ) {
            
            // strip leading underscore
            $variableName = substr( $variableName, 1 );
            
            $method = 'get' . ucfirst( $variableName );
            
            if( method_exists( $this, $method ) ) {
                $options[$variableName] = $this->$method();
            }
        }
        
        return $options;
    }
}

?>

<?php

/**
 * Description of CenterRequest
 *
 * @author JimS
 */
class Connect_CenterRequest {
    
    protected $_address1;
    protected $_searchOptions;
    protected $_latitude;
    protected $_longitude;
    
    // should we test for open centers
    protected $_testIsOpen;
    
    // the time to test open centers against
    protected $_testTime; 
        
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        
        if( is_array($this->_searchOptions) &&
                in_array( 'open', $this->_searchOptions ) ) {
            $this->_testIsOpen = true;
        }
        
        $this->_testTime = time();
    }
    
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Connect_Exception('Invalid center request property ' . $name . ' and method ' . $method );
        }
        $this->$method($value);
    }
    
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Connect_Exception('Invalid center request property ' . $name . ' and method ' . $method );
        }
        return $this->$method();
    }
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getAddress1() {
        return $this->_address1;
    }

    public function setAddress1($address) {
        $this->_address1 = $address;
    }
    
    public function getSearchOptions() {
        return $this->_searchOptions;
    }
    
    public function setSearchOptions($searchOptions) {
        $this->_searchOptions = $searchOptions;
    }
    
    public function getLatitude() {
        return $this->_latitude;
    }
    
    public function setLatitude($lat) {
        $this->_latitude = $lat;
    }
    
    public function getLongitude() {
        return $this->_longitude;
    }
    
    public function setLongitude($lng) {
        $this->_longitude = $lng;
    }
    
    public function getTestIsOpen() {
        return $this->_testIsOpen;
    }
    
    public function setTestIsOpen($isOpen) {
        $this->_testIsOpen = $isOpen;
    }
    
    public function getTestTime(){
        return $this->_testTime;
    }

    public function setTestTime($testTime){
        $this->_testTime = $testTime;
    }
}

?>

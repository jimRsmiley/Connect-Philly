<?php

class Connect_Position {
    
    //protected $_data = array( 'lat' => '', 'lng' => '', 'precision' => '');
    protected $_lat;
    protected $_lng;
    protected $_precision;
    
    public function __construct( $lat, $lng, $precision = null ) {
        $this->_lat = $lat;
        $this->_lng = $lng;
        $this->_precision = $precision;
    }
    public function setLat( $lat ) {
        $this->_lat = $lat;
    }
    
    public function getLat() {
        return $this->_lat;
    }
    
    public function setLng( $lng ) {
        $this->_lng = $lng;
    }
    
    public function getLng() {
        return $this->_lng;
    }
}
?>

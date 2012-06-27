<?php

/**
 * A computer center that was created as a result of a search in the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_FoundCenter extends Connect_ComputerCenter {
    
    protected $_request;
    protected $_distanceFromRequest;
    protected $_openStatus;
    
    public function __construct( $options, Connect_CenterRequest $request,
            $timestamp = null ) {
        
        parent::__construct( $options );
        $this->_request = $request;
        $this->_distanceFromRequest 
                = self::roundDistance( $this->getDistanceFromRequest(), 1 );
        
        if( $timestamp  == null ) {
            $timestamp = time();
        }
        
        $this->_openStatus = $this->getOpenStatus($timestamp);
    }
    
    public function getDistanceFromRequest() {
        return Connect_GISDistanceCalculator::distance(
                $this->getLatitude(), $this->getLongitude(),
                $this->_request->getLatitude(), $this->_request->getLongitude()
                 );
    }
    
    public static function roundDistance( $number, $precision ) {
        return round( $number, $precision );
    }
    
    public function toArray() {
        $options = parent::toArray();
        
        $options['openStatus'] = $this->_openStatus;
        
        return $options;
    }
}

?>

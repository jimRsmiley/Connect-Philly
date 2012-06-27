<?php

/**
 * Description of CenterResponse
 *
 * @author JimS
 */
class Connect_SMS_Response_FoundCenter extends Connect_SMS_Response {
    
    protected $_distance;
    protected $_foundCenter;
    
    public function __construct( Connect_CenterRequest $request,
     Connect_ComputerCenter $foundCenter ) 
    {
        if( empty( $foundCenter ) ) {
            throw new Connect_Exception('foundCenter cannot be empty');
        }
        
        $this->_centerRequest = $request;
        $this->_foundCenter = $foundCenter;
    }
    
    public function getDistance() {
        return $this->_distance;
    }
    
    public function setDistance($distance) {
        $this->_distance = $distance;
    }
    
    public function getFoundCenter() {
        return $this->_foundCenter;
    }
    
    public function getMessage() {
        
        $center = $this->getFoundCenter();

        $nextCenterNum = 0;
        if( $this->getCenterRequest() instanceof Connect_SMS_Request_NextCenterRequest ) {
            $nextCenterNum = (int)$this->getCenterRequest()->getNextCenterNum();
        }
        
        $wifi = $center->getHasWifiAccess();
        $phone = $center->getCenterPhoneNumber();
        $testTime = $this->getCenterRequest()->getTestTime();
        $openStatus = $center->getOpenStatus( $testTime );
        $isOpenStr = self::getOpenStatusString($openStatus);
        $wifi           = ( empty($wifi) ? '' : 'Wifi' );
        $phone          = ( empty($phone) ? '' : ' Tel.: '.$phone );
        
        $msg =  $center->getLocationTitle() . "\n"
                . $center->getAddress1() . "\n"
                . $wifi 
                . $phone
                . $isOpenStr;
        
        return $msg."\n".self::successMessageSuffix(($nextCenterNum+1));
    }
    
    public static function getOpenStatusString( $openStatus ) {
        if( $openStatus == Connect_ComputerCenter_OpenStatus::$OPEN ) {
            return ' Open Now';
        }
        else if($openStatus == Connect_ComputerCenter_OpenStatus::$CLOSED ) {
            return ' Closed Now';
        }
        else {
            return '';
        }
    }
    
    public static function successMessageSuffix($nextNum) {
        $msgSuffix = 'Send \'' . $nextNum 
                . '\' for next location. Text \'help\''
                . ' for search options and more info';
        return $msgSuffix;
    }
}
?>

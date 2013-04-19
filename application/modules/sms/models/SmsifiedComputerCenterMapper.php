<?php

/**
 * handle the data provided by the CenterRequestController and return valid
 * responses
 *
 * given a userAddress, will populate that object with the lat/lng
 * 
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_SmsifiedComputerCenterMapper extends Connect_ComputerCenterMapper {
    
    protected $lastAddressRequest = null;
    
    public function getCenter(  Connect_UserAddress $userAddress, 
                                $searchTerms = null, 
                                $nextCenterNum = 0, 
                                $testTime = null ) 
    {
        if( empty( $userAddress ) ) {
            throw new InvalidArgumentException( "address may not be null" );
        }
        
        $position = Connect_PhillyGeocoder::geocode($userAddress->getAddress());
        
        if( $position == null ) {
            throw new Sms_Model_Exception_BadAddress();
        }
        
        $userAddress->setLatitude($position->getLat());
        $userAddress->setLongitude($position->getLng());
        
        if( !empty( $testTime ) ) {
            $foundCenters = $this->getOpenCenters( 
                                $position, 
                                $searchTerms,
                                $testTime,
                                $numCenters = 1, 
                                $nextCenterNum
                            );
        }
        else {
            // pass it to the regular response builder
            $foundCenters = $this->getCenters( 
                    $position, 
                    $searchTerms, 
                    $numCenters = 1, 
                    $nextCenterNum
                );
        }

        if( count($foundCenters) > 0 ) {
            return $foundCenters[0];
        }
        else {
            return null;
        }
    }
    
    public function nextCenter( $nextCenterNum, $requesterAddress ) {

        if( !is_numeric($nextCenterNum) ) {
            throw new InvalidArgumentException("nextCenterNum must be numeric");
        }
        else if( empty( $requesterAddress ) ) {
            throw new InvalidArgumentException("requester address may not be null" );
        }
        
        $message = Connect_SMS_PastMessageFile::getLastMessage( 
                                        $requesterAddress );
        

        if( $message == null ) {
            return null;
        }
        $inboundMessage = new Connect_SMS_InboundMessage();
        $inboundMessage->setMessage($message);
        
        $request = new Sms_Model_Request_Smsified();
        $request->setInboundMessage($inboundMessage);
        
        $center = $this->getCenter($request->getUserAddress(),
                                $request->getSearchTerms(),
                                $nextCenterNum);
        
        return $center;
    }
}

?>

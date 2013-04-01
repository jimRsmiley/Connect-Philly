<?php

/**
 * handle the data provided by the CenterRequestController and return valid
 * responses
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_SmsifiedComputerCenterMapper {
    
    protected $lastAddressRequest = null;
    
    public function getCenter(  $address, 
                                $searchTerms = null, 
                                $nextCenterNum = 0, 
                                $testTime = null ) 
    {
        if( empty( $address ) ) {
            throw new InvalidArgumentException( "address may not be null" );
        }
        
        $position = Connect_PhillyGeocoder::geocode($address);
        
        if( $position == null ) {
            throw new Sms_Model_Exception_BadAddress();
        }
        
        $centerMapper = new Connect_ComputerCenterMapper();

        if( !empty( $testTime ) ) {
            $foundCenters = $centerMapper->getOpenCenters( 
                                $position, 
                                $searchTerms,
                                $testTime,
                                $numCenters = 1, 
                                $nextCenterNum
                            );
        }
        else {
            // pass it to the regular response builder
            $foundCenters = $centerMapper->getCenters( 
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
    
    public function nextCenter( $nextCenterNum, 
                            Connect_TelephoneNumber $requesterAddress ) {

        if( !is_numeric($nextCenterNum) ) {
            throw new InvalidArgumentException("nextCenterNum must be numeric");
        }
        else if( empty( $requesterAddress ) ) {
            throw new InvalidArgumentException("requester address may not be null" );
        }
        
        $message = Connect_SMS_PastMessageFile::getLastMessage( 
                                        $requesterAddress );
        

        if( $message == null ) {
            print "no previous message exists";
            return null;
        }
        $inboundMessage = new Connect_SMS_InboundMessage();
        $inboundMessage->setMessage($message);
        
        $request = new Sms_Model_Request_Smsified();
        $request->setInboundMessage($inboundMessage);
        
        $center = $this->getCenter($request->getAddress(),
                                $request->getSearchTerms(),
                                $nextCenterNum);
        
        return $center;
    }
}

?>

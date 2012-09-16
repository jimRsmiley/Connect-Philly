<?php

/**
 * Description of SmsifiedComputerCenterMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Model_SmsifiedComputerCenterMapper {
    
    public function getCenter(  $address, 
                                $searchTerms = null, 
                                $nextCenterNum = 0, 
                                $testTime = null ) 
    {
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
    
    public function nextCenter( $nextCenterNum, $requesterAddress ) {

        $message = Connect_SMS_PastMessageFile::getLastEntry( 
                                        $requesterAddress );
        
        $inboundMessage = new Connect_SMS_InboundMessage();
        $inboundMessage->setMessage($message);
        
        $request = new Connect_Controller_Request_Smsified();
        $request->setInboundMessage($inboundMessage);
        
        $center = $this->getCenter($request->getAddress(),
                                $request->getSearchTerms(),
                                $nextCenterNum);
        
        return $center;
    }
}

?>

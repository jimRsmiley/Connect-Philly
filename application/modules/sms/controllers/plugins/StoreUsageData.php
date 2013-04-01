<?php

/**
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Sms_Controller_Plugin_StoreUsageData 
    extends Zend_Controller_Plugin_Abstract{

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        
        // log only on center-request controller
        if( 'center-request' != $request->getControllerName() ) {
            return;
        }
        
        $senderAddress = $request->getInboundMessage()->getSenderAddress();
        
        if( Sms_Model_PhoneNumberWhitelist::exists($senderAddress) 
                && APPLICATION_ENV == 'production' ) 
        {
            return;
        }
        
        $response = $this->getResponse();
        
        $properties = array( 
            'timestamp' => Connect_AbstractMapper::getFusionTableTimestamp( time() ),
            'centerRequestText' => $request->getAddress1(),
            'latitude'          => $response->getAddressRequestLat(),
            'longitude'         => $request->getAddressRequestLng()
        );
        $usageData = new Connect_UsageData($properties);

        $mapper = new Connect_UsageDataMapper();
        $mapper->save( $usageData );
    }
}

?>

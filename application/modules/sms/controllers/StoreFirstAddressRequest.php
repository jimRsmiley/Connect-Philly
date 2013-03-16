<?php

/**
 * A user of Connect Philly may request multiple 
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class StoreFirstAddressRequest {
    
    /**
     * if the request is an address, and we successfully geocoded it
     * @param type $request
     */
    public function postDispatch( $request ) {
        
        print "we need to be storing this message";
        exit;
        if( $request->getType() == Sms_Model_Request_Type::$ADDRESS
                && $this->getResponse()->getType() == 'success' ) {
            
            /*
             *  only time you store the address
             */
            Connect_SMS_PastMessageFile::store( 
                    $this->getRequest()->getInboundMessage() );
        }
    }
    
    
    // XXX this should be a post dispatch plugin
    public static function storeUsageData(Connect_CenterRequest $request, $senderAddress ) {
        
        if( !preg_match( "/2677389559/", $senderAddress ) 
                && APPLICATION_ENV == 'production' ) 
        {
            $properties = array( 
                'timestamp' => Connect_AbstractMapper::getFusionTableTimestamp( time() ),
                'centerRequestText' => $request->getAddress1(),
                'latitude'          => $request->getLatitude(),
                'longitude'         => $request->getLongitude()
            );
            $usageData = new Connect_UsageData($properties);

            $mapper = new Connect_UsageDataMapper();
            $mapper->save( $usageData );
        }
    }
}

?>

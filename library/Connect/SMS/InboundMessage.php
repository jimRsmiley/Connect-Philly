<?php

/*
 * Convenience class that parses inbound SMSified JSON into a simple object.
 */
class Connect_SMS_InboundMessage {

    // Class properties.
    protected $timeStamp;
    protected $destinationAddress;
    protected $message;
    protected $messageId;
    protected $senderAddress;
    protected $json;

    // Class constructor.
    public function __construct( $json = null ) {

        if( !empty( $json ) ) {
            $this->json = $json;
            $notification = json_decode($json);
            $this->timeStamp = $notification->inboundSMSMessageNotification->inboundSMSMessage->dateTime;
            $this->destinationAddress = new Connect_TelephoneNumber($notification->inboundSMSMessageNotification->inboundSMSMessage->destinationAddress );
            $this->message = $notification->inboundSMSMessageNotification->inboundSMSMessage->message;
            $this->messageId = $notification->inboundSMSMessageNotification->inboundSMSMessage->messageId;
            $this->senderAddress = new Connect_TelephoneNumber( $notification->inboundSMSMessageNotification->inboundSMSMessage->senderAddress );
        }
    }
	
	public function getTimeStamp() {
		return $this->timeStamp;
	}
	
    public function setTimeStamp( $timestamp ) {
        $this->timeStamp = $timestamp;
    }

	public function getDestinationAddress() {
		return $this->destinationAddress;
	}
	
    public function setDestinationAddress($address) {
        $this->destinationAddress = new Connect_TelephoneNumber( $address );
        //$this->destinationAddress = $address;
    }
        
	public function getMessage() {
		return $this->message;
	}
	
    public function setMessage( $msg ) {
        $this->message = $msg;
    }
        
	public function getMessageId() {
		return $this->messageId;
	}
        
    public function setMessageId( $id ) {
        $this->messageId = $id;
    }
	
	public function getSenderAddress() {
		return $this->senderAddress;
	}
        
    public function setSenderAddress( $address ) {
        $this->senderAddress = new Connect_TelephoneNumber( $address );
        //$this->senderAddress = $address;
    }

    public function getJSON() {

        $senderString = ($this->senderAddress != null ? $this->senderAddress->__toString() : "" );
        $destinationString = ($this->destinationAddress != null ? $this->destinationAddress->__toString() : "" );
        if( empty($this->json) ) {
            $array = array( 'inboundSMSMessageNotification' =>
                        array( 'inboundSMSMessage' =>
                            array( 'destinationAddress' => $destinationString,
                                'senderAddress' => $senderString,
                                'message'       => $this->message,
                                'messageId'     => $this->messageId,
                                'dateTime'      => $this->timeStamp,
                                )
                            )
                        );

            $this->json = json_encode( $array );


        }

        return $this->json;
    }
    
    /**
     * wrap the getJSON function
     * @return string
     */
    public function __toString() {
        return $this->getJSON();
    }

}

?>
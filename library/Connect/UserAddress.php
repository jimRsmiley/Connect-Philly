<?php

/**
 * Description of Connect_UserAddress
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_UserAddress {
    
    protected $address;
    protected $latitude;
    protected $longitude;
    
    public function __construct( $data ) {
        
        if( is_array( $data ) ) {
            
            if(array_key_exists('address',$data) ) {
                $this->address = $data['address'];
            }
            
            if(array_key_exists('latitude',$data) ) {
                $this->address = $data['latitude'];
            }
            
            if(array_key_exists('longitude',$data) ) {
                $this->address = $data['longitude'];
            }
        }
    }
	public function getAddress(){
		return $this->address;
	}

	public function setAddress($address){
		$this->address = $address;
	}

	public function getLatitude(){
		return $this->latitude;
	}

	public function setLatitude($latitude){
		$this->latitude = $latitude;
	}

	public function getLongitude(){
		return $this->longitude;
	}

	public function setLongitude($longitude){
		$this->longitude = $longitude;
	}
}

?>

<?php

/**
 * Description of UsageData
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_UsageData extends Connect_AbstractObject {
    
	private $timestamp = null;
	private $centerRequestText = null;
	private $latitude = null;
	private $longitude = null;
    
	public function getTimestamp(){
		return $this->timestamp;
	}

	public function setTimestamp($timstamp){
		$this->timestamp = $timstamp;
	}

	public function getCenterRequestText(){
		return $this->centerRequestText;
	}

	public function setCenterRequestText($centerRequestText){
		$this->centerRequestText = $centerRequestText;
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
    
    /**
     * NOTE: needed to get protected/private class variable names related to
     *  get/set Properties
     * 
     * @return array
     */
    public function getClassVariableNames() {
        return array_keys( get_class_vars( get_class($this) ) );
    }
}

?>

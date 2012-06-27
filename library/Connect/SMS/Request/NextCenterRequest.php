<?php

/**
 * Description of NextCenterRequest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_SMS_Request_NextCenterRequest extends Connect_CenterRequest {
    
    protected $_nextCenterNum = null;
    
    public function getNextCenterNum() {
        return $this->_nextCenterNum;
    }
    
    public function setNextCenterNum($nextCenterNum) {
        $this->_nextCenterNum = $nextCenterNum;
    }
}

?>

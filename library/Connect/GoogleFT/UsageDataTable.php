<?php
/**
 * Description of UsageDataTable
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_GoogleFT_UsageDataTable extends Connect_GoogleFT_FusionTable {
    
    public function insert( $properties ) {
        
        if( empty( $properties ) ) {
            throw new Exception("properties may not be empty" );
        }
        
        $sql = Connect_GoogleFT_SQLBuilder::insert($this->_tableId, $properties);
        $result = $this->makeAPICall( $sql );              

        return $result;
    }
}

?>

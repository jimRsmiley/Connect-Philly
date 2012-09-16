<?php

/**
 * because Google Fusion Tables don't include the rowid in cases when using
 * "SELECT *", this class builds a list of column names on constructing, then
 * cats all the names along with rowid into the select statement 
 */
class Connect_GoogleFT_ComputerCenterTable extends Connect_GoogleFT_FusionTable {

    protected static $_locationColumn = 'Longitude';

    /**
     * insert data into the google fusion table
     * 
     * It adds the current timestamp to the computer center when entered.
     *
     * @param mixed $data - an associative array
     * @return boolean false on failure
     */
    public function insert($data) {

        $timestamp = date('Y-m-d H:i:s');

        $data['Timestamp'] = $timestamp;

        if( empty( $data ) ) {
            throw new InvalidArgumentException( 'data may not be null' );
        }

        $ftclient = $this->_ftclient;
        $sql = Connect_GoogleFT_SQLBuilder::insert($this->_tableId, $data);
        $result = $ftclient->query( $sql );              

        if (preg_match('/Error 400/', $result)) {
            throw new Exception($result);
        }

        return true;
    }

    public function getSelectStatement( Connect_Position $position, 
            $searchTerms = null,
            $limit = null, $offset = null, $includeRowId = false ) {
        $conditions = "'Pending Confirmation' NOT EQUAL TO 'true' AND";

        $limitStr = '';
        if ( !empty($limit) ) {
            $limitStr = " LIMIT $limit";
        }

        $offsetStr = '';
        if ( !empty($offset) ) {
            $offsetStr = " OFFSET $offset";
        }

        $orderBy = sprintf(
                " ORDER BY ST_DISTANCE('%s', LATLNG( %s, %s ) )%s%s", 
                self::$_locationColumn, 
                $position->getLat(), $position->getLng(), 
                $offsetStr, $limitStr);

        

        if ($searchTerms != null) {
            foreach ($searchTerms as $term) {
                
                if( strtolower($term) != 'open' ) {
                    $conditions .= ' '
                            . Connect_GoogleFT_SearchTerms::getFtSql($term)
                            . ' AND';
                }
            }
        }
        // get rid of that last trailing ' AND'
        if ($conditions != null) {
            $conditions = substr($conditions, 0, strlen($conditions) - 4);
        }

        $cols = null;
        if( $includeRowId ) {
            // this will invoke a database call to lookup the column names
            $cols = $this->getColumnNames();
        }
        
        $sql = Connect_GoogleFT_SQLBuilder::select(
                        $this->_tableId, $cols, $conditions, $orderBy);
        
        return $sql;
    }
    

}



?>

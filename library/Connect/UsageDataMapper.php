<?php

/**
 * Description of UsageDataMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Connect_UsageDataMapper extends Connect_AbstractMapper {
    
    protected $table = null;
    
    public function __construct() {}
    
    public function save( Connect_UsageData $usageData ) {
        $table = $this->getFusionTable();
        
        $data = $this->getInsertData($usageData);
        
        $table->insert($data);
    }
    
    protected function getFusionTable() {
        
        if( $this->table == null )
        {
            $config = Zend_Registry::get('configuration');

            $this->table = new Connect_GoogleFT_UsageDataTable( 
                    $config->gmap->usageData->ftId, 
                    $config->google->user,
                    $config->google->pass
                  );
        }

        return $this->table;
    }
}

?>

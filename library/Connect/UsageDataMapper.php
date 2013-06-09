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
                array(
                    'tableId'   => $config->gmap->usageData->ftId, 
                    'clientId'  => $config->google->clientLogin->clientId,
                    'emailAddress'  => $config->google->clientLogin->emailAddress,
                    'privateKeyFile'    => $config->google->clientLogin->privateKeyFile
                  ));
        }

        return $this->table;
    }
}

?>

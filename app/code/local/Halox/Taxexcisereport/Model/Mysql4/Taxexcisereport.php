<?php

class Halox_Taxexcisereport_Model_Mysql4_Taxexcisereport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the taxexcisereport_id refers to the key field in your database table.
        $this->_init('taxexcisereport/taxexcisereport', 'taxexcisereport_id');
    }
}
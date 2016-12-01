<?php

class Halox_Taxexcisereport_Model_Taxexcisereport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('taxexcisereport/taxexcisereport');
    }
}
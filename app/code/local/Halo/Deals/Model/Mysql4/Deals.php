<?php
class Halo_Deals_Model_Mysql4_Deals extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("deals/deals", "id");
    }
}
<?php

class Halox_AgeVerification_Model_Mysql4_Ageverificationdetails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ageverification/ageverificationdetails');
    }
}
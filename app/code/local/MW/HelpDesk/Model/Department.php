<?php

class MW_HelpDesk_Model_Department extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/department');
    }
}
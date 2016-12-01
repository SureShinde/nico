<?php

class MW_HelpDesk_Model_Mysql4_Member_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/member');
    }
}
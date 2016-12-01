<?php

class MW_HelpDesk_Model_Mysql4_Deme extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the helpdesk_id refers to the key field in your database table.
        $this->_init('helpdesk/deme', 'department_member');
    }
}
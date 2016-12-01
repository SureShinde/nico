<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.9.2
 * @license:     QvHpbEc0oOzK3qo2hlmyDcWVqjqnkaNA9m9UogV4wV
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Model_Mysql4_Editor_Tab_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/editor_tab');
    }

    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter('role_id', $roleId);
        $this->load();
        return $this;
    }

    public function duplicateProductTabPermissions($oldRoleId, $newRoleId)
    {
        $oldTabs = $this->loadByRoleId($oldRoleId);
        
        foreach($oldTabs as $tab)
        {
            $tab->setData('id', null);
            $tab->setData('role_id', $newRoleId);
            $tab->setData('tab_code',$tab->getTabCode());
            $tab->save();
        }        
    }
}
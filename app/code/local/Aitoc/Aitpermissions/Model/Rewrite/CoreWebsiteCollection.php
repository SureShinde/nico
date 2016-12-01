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
class Aitoc_Aitpermissions_Model_Rewrite_CoreWebsiteCollection extends Mage_Core_Model_Mysql4_Website_Collection
{
    public function toOptionHash()
    {
        $role = Mage::getSingleton('aitpermissions/role');
        if ($role->isPermissionsEnabled())
        {
            $this->addFieldToFilter('website_id', array('in' => $role->getAllowedWebsiteIds()));
        }

        return parent::toOptionHash();
    }
}
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
class Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Advanced extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/permissions_advanced.phtml');
        $this->_withProductCount = false;
    }

    protected function _prepareLayout()
    {
        $this->setChild('stores', $this->getLayout()->createBlock('aitpermissions/adminhtml_store_switcher'));
        
        $WebsiteIds = array();
        $RoleCollection = Mage::getModel('aitpermissions/advancedrole')->getCollection()->loadByRoleId($this->getRequest()->getParam('rid'));
        if ($RoleCollection->getItems()) 
        {
            foreach ($RoleCollection->getItems() as $role)
            {
                if ($role->getWebsiteId())
                {
                    $WebsiteIds[] = $role->getWebsiteId();
                }
            }
        }
        
        $this->setChild('websites', $this->getLayout()->createBlock('aitpermissions/adminhtml_website_select')
        ->setCurrentWebsiteIds($WebsiteIds));
        
        $this->setChild('options', $this->getLayout()->createBlock('aitpermissions/adminhtml_options'));
        
        return $this;
    }
    
    public function getScope()
    {
        $RoleCollection = Mage::getModel('aitpermissions/advancedrole')->getCollection()->loadByRoleId($this->getRequest()->getParam('rid'));
        if ($RoleCollection->getItems()) 
        {
            foreach ($RoleCollection->getItems() as $role)
            {
                if ($role->getStoreId())
                {
                    return 'store';
                }
            }
            return 'website';
        }
        return 'disabled';
    }
    
    public function isReadonly()
    {
        return false;
    }

    public function getStores()
    {
        $stores = Mage::app()->getStores(true);
        return $stores;
    }
    
     /**
     * Get websites
     *
     * @return array
     */
    public function getWebsitesGroups()
    {
        $websites = Mage::app()->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) 
        {
            foreach ($websites as $websiteId => $website) 
            {
                if (!in_array($websiteId, $websiteIds)) 
                {
                    unset($websites[$websiteId]);
                }
            }
        }
        $groups = array();
        foreach ($websites as $website)
        {
            foreach ($website->getGroups() as $group)
            {
                $groups[] = $group;
            }
        }
        return $groups;
    }
}
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
class Aitoc_Aitpermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isShowingAllProducts()
    {
        return Mage::getStoreConfig('admin/general/showallproducts');
    }

    public function isShowingAllCustomers()
    {
        return Mage::getStoreConfig('admin/general/showallcustomers');
    }

    public function isShowProductOwner()
    {
        return Mage::getStoreConfig('admin/general/show_admin_on_product_grid');
    }

    public function isAllowedDeletePerWebsite()
    {
        return Mage::getStoreConfig('admin/general/allowdelete_perwebsite');
    }

    public function isAllowedDeletePerStoreview()
    {
        return Mage::getStoreConfig('admin/general/allowdelete');
    }

    public function isShowingProductsWithoutCategories()
    {
        return Mage::getStoreConfig('admin/general/allow_null_category');
    }

    /**
     * backward compatibility with Shopping Assistant
     */
    public function getAllowedCategories()
    {
        return Mage::getSingleton('aitpermissions/role')->getAllowedCategoryIds();
    }
    
    public function isQuickCreate()
    {
        return Mage::app()->getRequest()->getActionName() == 'quickCreate' ? true : false;
    }

    /**
     * retrieve tabs from admin product page
     */
    public function getProductTabs()
    {
        return array(
            'inventory' => 'Inventory',
            'websites' => 'Websites',
            'categories' => 'Categories',
            'related' => 'Related',
            'upsell' => 'Upsell',
            'crosssell' => 'Crosssell',
            'productalert' => 'Product Alerts',
            'reviews' => 'Product Reviews', 
            'tags' => 'Product Tags',
            'customers_tags' => 'Customers Tagged Product',
            'customer_options' => 'Custom Options');
    }
    
    public function getAttributePermission()
    {
        $user = Mage::getSingleton('admin/session')->getUser();

        $aitAttributeModel = Mage::getSingleton('aitpermissions/editor_attribute');
        return $aitAttributeModel->getAttributePermissionByRole($user->getRole()->getRoleId());
    }
}
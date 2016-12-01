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
class Aitoc_Aitpermissions_Model_Rewrite_CatalogProductAction extends Mage_Catalog_Model_Product_Action
{
    public function updateAttributes($productIds, $attrData, $storeId)
    {
        if (isset($attrData['status']) &&
            $this->_isUpdatingStatus() &&
            Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() &&
            Mage::getStoreConfig('admin/su/enable')
        )
        {
            if ($attrData['status'] == Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING)
            {
                Mage::throwException(Mage::helper('core')->__('This status cannot be used in mass action'));
                return $this;
            }
			
			foreach ($this->_getProductIdsToApprove($productIds) as $productId)
			{
			   Mage::getModel('aitpermissions/approve')->approve($productId, $attrData['status']);
			}
        }
        
        return parent::updateAttributes($productIds, $attrData, $storeId);
    }

    private function _isUpdatingStatus()
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();

        return ($controllerName == 'catalog_product' && $actionName == 'massStatus') ||
            ($controllerName == 'catalog_product_action_attribute' && $actionName == 'save');
    }

    private function _getProductIdsToApprove($productIds)
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($productIds)
            ->addAttributeToFilter('status', array('neq' => Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING));

        $productIdsToApprove = array();

        foreach ($productCollection as $product)
        {
            $productIdsToApprove[] = $product->getId();
        }

        return $productIdsToApprove;
    }
}
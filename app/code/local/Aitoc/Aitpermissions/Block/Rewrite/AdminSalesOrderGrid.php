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
class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled())
		{
			$allowedStoreviews = $role->getAllowedStoreviewIds();
    		if (count($allowedStoreviews) <= 1 && isset($this->_columns['store_id']))
            {
                unset($this->_columns['store_id']);
            }
		}
        
		return $this;
	}
}
<?php

/**
 * parent class with common methods for tab types
 */
abstract class Halox_BulkOrder_Block_Tab_Type_Abstract extends Mage_Core_Block_Template
{

	protected $_currentEntity;

	/**
	 * get default helper
	 */
	public function _getHelper()
	{
		return Mage::helper('halox_bulkorder');
	}

	/**
	 * get current store id
	 */
	public function getStoreId()
	{
		return Mage::app()->getStore()->getId();
	}
	
	public function getCurrentEntityId()
	{
		return Mage::registry('current_entity_id');
	}

	public function getCurrentTabType()
	{
		return Mage::registry('currrent_tab_type');
	}

	public function isMultiStep()
	{
		return Mage::registry('is_multi_step') ? true : false;
	}

	/**
	 * get the AJAX load url for the horizontal tabs	
	 */
	public function getTabContentLoadUrl()
	{
		
		return Mage::getUrl('bulkorder/grid/renderTabContent');
		
	}

	abstract public function getCurrentEntity();

	abstract public function getChildren($category, $columns = array());

}
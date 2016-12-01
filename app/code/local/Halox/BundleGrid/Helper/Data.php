<?php
class Halox_BundleGrid_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
	 * get current store instance
	 * @return Mage_App_Model_Store instance
	 */
	public function getCurrentStore()
	{
		return Mage::app()->getStore();
	}

	/**
	 * check if the module is enabled from the backend
	 * @return bool TRUE | FALSE
	 */
	public function isModuleActive()
	{
		return Mage::getStoreConfig('halox_bundlegrid/general_settings/enabled', $this->getCurrentStore()->getId());
	}	

}
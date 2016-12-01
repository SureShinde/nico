<?php
class Halox_CartItemNames_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * check if the module is enabled from the backend
	 * @return bool TRUE | FALSE
	 */
	public function isModuleActive($storeId)
	{
		return Mage::getStoreConfig('halox_cartitemnames/general_settings/enabled');
	}	
}
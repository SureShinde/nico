<?php

class Halox_WholesaleCartUpdate_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	const XML_CONFIG_PATH_MODULE_ENABLED = 'halox_wholesalecartupdate/general/enabled';

	/**
	 * is module is active for store scope passed in argument
	 * @param int $storeId id of the store
	 */
	public function isModuleActive($storeId)
	{
		return Mage::getStoreConfigFlag(static::XML_CONFIG_PATH_MODULE_ENABLED, $storeId);
	}
}
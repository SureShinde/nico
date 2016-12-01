<?php

class Halox_ExtendedCatalogFilter_Helper_Data extends Mage_Core_Helper_Data
{
	
	const ATTRIBUTE_CODE_CATALOG_PRODUCT_ENTITY = 'is_extended_catalog_product';
	const ATTRIBUTE_CODE_CUSTOMER_ENTITY = 'show_extended_catalog';

	const XML_PATH_MODULE_ENABLED = 'halox_extendedcatalogfilter/general_settings/enabled';

	/**
	 *  check if the module is enabled for the current store
	 */
	public function isModuleActive($storeId)
	{
		return Mage::getStoreConfigFlag(static::XML_PATH_MODULE_ENABLED, $storeId);
	}
}
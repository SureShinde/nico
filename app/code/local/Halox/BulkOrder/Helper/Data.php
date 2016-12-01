<?php
/**
 * @module Halox_BulkOrder
 */

class Halox_BulkOrder_Helper_Data extends Mage_Core_Helper_Abstract
{

	const GRID_TAB_TYPE_HORIZONTAL = 'HORIZONTAL';
	const GRID_TAB_TYPE_VERTICAL = 'VERTICAL';

	/**
	 * get current store instance
	 * @return Mage_App_Model_Store instance
	 */
	public function getCurrentStore()
	{
		return Mage::app()->getStore();
	}


	/**
	 * fetch the base category id configured in the System->Configuration section
	 * @return int base category id
	 */
	public function getBaseCategoryIds()
	{
		$configuredIds = Mage::getStoreConfig('halox_bulkorder/general_settings/base_category_ids', $this->getCurrentStore()->getId());
		
		return explode(',', $configuredIds);
	}


	/**
	 * check if the module is enabled from the backend
	 * @return bool TRUE | FALSE
	 */
	public function isModuleActive()
	{
		return Mage::getStoreConfig('halox_bulkorder/general_settings/enabled', $this->getCurrentStore()->getId());
	}


	/**
	 * get base attribute id x axis
	 * @return int 
	 */
	public function getBaseAttributeAxisX()
	{
		return Mage::getStoreConfig('halox_bulkorder/general_settings/base_attribute_axis_x', $this->getCurrentStore()->getId());
	}


	/**
	 * get base attribute id y axis
	 * @return int 
	 */
	public function getBaseAttributeAxisY()
	{
		return Mage::getStoreConfig('halox_bulkorder/general_settings/base_attribute_axis_y', $this->getCurrentStore()->getId());
	}

	/**
	 * @return current store currency symbol
	 */
	public function getStoreCurrencySymbol()
	{
		return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
	}

	/**
	 * check if quote item has bulk order buy request option
	 */
	public function getBulkRequestOptionByQuoteItem($quoteItem)
	{
		$bulkBuyRequest = array();

		$option = $quoteItem->getOptionByCode('info_buyRequest');
		$value = unserialize($option->getValue());
		if(isset($value['halox_bulk_buyRequest'])){
			$bulkBuyRequest = unserialize($value['halox_bulk_buyRequest']);
		}

		return $bulkBuyRequest;
	}

	/**
	 * append parameter to a given url
	 */
	public function appendParameterToUrl($url, $urlParams = array())
	{
		$params = array();
		
		$urlParts = parse_url($url);
		if(isset($urlParts['query'])){
			parse_str($urlParts['query'], $params);
		} 

		if( ! empty($urlParams)){
			$params = array_merge($params, $urlParams);
		}

		if( ! empty($params)){
			$urlParts['query'] = http_build_query($params);
		}

		return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $urlParts['query'];
	}
}
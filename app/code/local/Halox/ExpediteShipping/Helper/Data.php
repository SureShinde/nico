<?php
class Halox_ExpediteShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	const LOG_FILE = 'expediteshipping.log';

	const XML_PATH_EXTENSION_ENABLED = 'halox_expediteshipping/general_settings/enabled';
	const XML_PATH_EXPEDITE_BASE_METHOD = 'halox_expediteshipping/general_settings/expedite_base_method';
	const XML_PATH_EXPEDITE_APPLY_MARKUP = 'halox_expediteshipping/general_settings/apply_markup';
	const XML_PATH_EXPEDITE_MARKUP_TYPE = 'halox_expediteshipping/general_settings/markup_type';
	const XML_PATH_EXPEDITE_MARKUP_AMOUNT = 'halox_expediteshipping/general_settings/markup_amount';
	const XML_PATH_DEBUG_MODE_ENABLED = 'halox_expediteshipping/general_settings/debug_mode';

	/**
	 * check if module is active for the current store scope
	 */
	public function isModuleActive($storeId)
	{	
		return Mage::getStoreConfigFlag(static::XML_PATH_EXTENSION_ENABLED, $storeId);
	}

	/**
	 * get base method for expedite shipping
	 */
	public function getBaseExpediteShippingMethod($storeId)
	{
		return Mage::getStoreConfig(static::XML_PATH_EXPEDITE_BASE_METHOD, $storeId);
	}

	/**
	 * get base method for expedite shipping
	 */
	public function canApplyMarkup($storeId)
	{
		return Mage::getStoreConfigFlag(static::XML_PATH_EXPEDITE_APPLY_MARKUP, $storeId);
	}

	/**
	 * get base method for expedite shipping
	 */
	public function getMarkupType($storeId)
	{
		return Mage::getStoreConfig(static::XML_PATH_EXPEDITE_MARKUP_TYPE, $storeId);
	}

	/**
	 * get base method for expedite shipping
	 */
	public function getMarkupAmount($storeId)
	{
		return Mage::getStoreConfig(static::XML_PATH_EXPEDITE_MARKUP_AMOUNT, $storeId);
	}

	/**
	 * get base method for expedite shipping
	 */
	public function isDebugModeEnabled($storeId)
	{
		return Mage::getStoreConfigFlag(static::XML_PATH_DEBUG_MODE_ENABLED, $storeId);
	}

	/**
	 * @see Mage_Sales_Model_Quote_Address::requestShippingRates() method
	 * @return Mage_Shipping_Model_Rate_Request
	 */
	public function buildShippingRequest($shippingAddress)
	{
		$request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($shippingAddress->getAllItems());
        $request->setDestCountryId($shippingAddress->getCountryId());
        $request->setDestRegionId($shippingAddress->getRegionId());
        $request->setDestRegionCode($shippingAddress->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($shippingAddress->getStreet($shippingAddress::DEFAULT_DEST_STREET));
        $request->setDestCity($shippingAddress->getCity());
        $request->setDestPostcode($shippingAddress->getPostcode());
        $request->setPackageValue($shippingAddress->getBaseSubtotal());
        $packageValueWithDiscount = $shippingAddress->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageValueWithDiscount);
        $request->setPackageWeight($shippingAddress->getWeight());
        $request->setPackageQty($shippingAddress->getItemQty());

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $shippingAddress->getBaseSubtotal() - $shippingAddress->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($shippingAddress->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($shippingAddress->getQuote()->getStore()->getId());
        $request->setWebsiteId($shippingAddress->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($shippingAddress->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($shippingAddress->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($shippingAddress->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($shippingAddress->getLimitCarrier());

        
    	$request->setBaseSubtotalInclTax($shippingAddress->getBaseSubtotalInclTax() + $shippingAddress->getBaseExtraTaxAmount());
		
		return $request;
	}
}
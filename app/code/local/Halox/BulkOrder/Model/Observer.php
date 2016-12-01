<?php 

/**
 * observer for the events subscribed by this module
 */
class Halox_BulkOrder_Model_Observer extends Varien_Object
{

	protected function _getHelper()
	{
		return Mage::helper('halox_bulkorder');
	}

	/**
	 * inject bulk order form layout handle checking the base category id
	 * configured in System configuration section
	 */
	public function injectBulkOrderFormHandle($observer)
	{
		$action = $observer->getEvent()->getAction();
		
		$isModuleEnabled = $this->_getHelper()->isModuleActive(); 
		if( ! $isModuleEnabled){
			return $this;
		}

		if($action->getFullActionName() != 'catalog_category_view'){
			return $this;
		}

		$currentCategory = Mage::registry('current_category');
		
		$baseCategoryIds = $this->_getHelper()->getBaseCategoryIds();

		if( ! $baseCategoryIds && ! $currentCategory){
			return $this;	
		}

		if(in_array($currentCategory->getId(), $baseCategoryIds)){
			$layout = $observer->getEvent()->getLayout();
			$layout->getUpdate()->addHandle('BULK_ORDER_FORM_HANDLE');	
		}

		return $this;
	}

	/**
	 * check if the product was added from E-Liquid Bulk Order form
	 * redirect user to E-Liquid form with configure_id parameter
	 */
	public function onCheckoutCartConfigure($observer)
	{

		$isModuleEnabled = $this->_getHelper()->isModuleActive(); 
		if( ! $isModuleEnabled){
			return $this;
		}

		$quoteItemId = (int) Mage::app()->getRequest()->getParam('id');
		if(! $quoteItemId){
			return $this;
		}

		$quoteItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($quoteItemId);
		if( ! $quoteItem){
			return $this;
		}

		$bulkBuyRequestOption = $quoteItem->getOptionByCode('info_buyRequest');
		
		if( ! $bulkBuyRequestOption){
			return $this;
		}

		if( ! $bulkBuyRequestOption = unserialize($bulkBuyRequestOption->getValue())){
			return $this;
		}

		if(! isset($bulkBuyRequestOption['halox_bulk_buyRequest'])){
			return $this;
		}

		$bulkBuyRequestOption = unserialize($bulkBuyRequestOption['halox_bulk_buyRequest']);

		if(!is_array($bulkBuyRequestOption) || count($bulkBuyRequestOption) <= 0){
			return $this;
		}

		$itemBaseCategoryId = $bulkBuyRequestOption['base_category_id'];

		$configBaseCategoryIds = Mage::helper('halox_bulkorder')->getBaseCategoryIds();
		
		if( ! in_array($itemBaseCategoryId, $configBaseCategoryIds)){
			return $this;
		}

		$baseCategory = Mage::getModel('catalog/category')->load($itemBaseCategoryId);
		if( ! $baseCategory){
			return $this;
		}

		$bulkOrderFormUrl = $this->_getHelper()->appendParameterToUrl($baseCategory->getUrl(), array(
			'configure_id' => $quoteItemId
		));
		
		Mage::app()->getResponse()->setRedirect($bulkOrderFormUrl)->sendResponse();
		
		return $this;	

	}

}
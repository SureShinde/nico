<?php

/**
 * Bulk Order Form Block instance
 */
class Halox_BulkOrder_Block_Form extends Mage_Catalog_Block_Category_View
{

	/**
	 * the quote item that got clicked to show order grid with quote items
	 */
	protected $_activeQuoteItem;

	/**
     * cache the array of horizontal tabs
     */
	protected $_horizontalTabs = array();

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

	/**
	 * get the add to cart url for the products in bulk
	 */
	public function getSubmitGridUrl()
	{
		return ! Mage::app()->getRequest()->getParam('configure_id') 
			? Mage::getUrl('bulkorder/grid/addtocart')
			: Mage::getUrl('bulkorder/grid/updatecart');
	}

	/**
	 * get immediate children categories
	 * @params array $columns columns to select
	 */
	public function getChildCategories($columns = array())
	{
		
		$collection = Mage::getModel('catalog/category')->getCollection();
		if( ! empty($columns)){
			$collection->addAttributeToSelect($columns);
		}
			
		$collection->addIdFilter($this->getCurrentCategory()->getChildren());

		return $collection;	

	}

	/**
	 * get the AJAX load url for the horizontal tabs	
	 */
	public function getTabContentLoadUrl($params = array())
	{
		
		return Mage::getUrl('bulkorder/grid/renderTabContent', $params);
		
	}

	/**
	 * get the cart url
	 */
	public function getCartUrl()
	{
		
		return Mage::getUrl('checkout/cart');
		
	}

	/**
	 * get the onepage checkout url
	 */
	public function getCheckoutUrl()
	{
		
		return Mage::getUrl('checkout/onepage');
		
	}

	/**
	 * get child categories for the base categories as JSON to be
	 * shown in bulk order grid as horizontal tabs
	 */
	public function getHorizontalTabs($columns, $asJSON = false)
	{

		if( ! $this->_horizontalTabs){

			$collection = $this->getChildCategories($columns);
			if($collection->getSize() > 0){
				$tabData = array();
				$tabIndex = 0;
				foreach($collection as $item){
					$tabData[$item->getId()] = $item->getData();
					$tabData[$item->getId()]['tab_index'] = $tabIndex;
					$tabIndex = $tabIndex + 1; 
				}

				$this->_horizontalTabs = $tabData;			
			}
		}
		

		return $asJSON ? $this->helper('core')->jsonEncode($this->_horizontalTabs) : $this->_horizontalTabs;	
	}

	/**
	 * get the bulk order grid loader image url
	 */
	public function getLoaderImageUrl()
	{
		return $this->getJsUrl('halox/bulkorder/images/grid-loader.gif');
	}

	/**
	 * get the bulk order grid category image url prefix
	 */
	public function getCategoryImageUrlPrefix()
	{
		return Mage::getBaseUrl('media') . 'catalog/category/';
	}

	/**
	 * get item to be configured
	 */
	public function getActiveQuoteItem()
	{
		if( ! $this->_activeQuoteItem){
			$quoteItemId = (int) Mage::app()->getRequest()->getParam('configure_id');
			if($quoteItemId){
				$this->_activeQuoteItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($quoteItemId); 
			}	
		}

		return $this->_activeQuoteItem;
		
	}

	/**
	 * get data saved at the time of first add to cart request
	 */
	public function getPreConfiguredDataAsJSON()
	{	
		
		$preConfiguredData = array();
		$quoteItem = null;

		$quoteItem = $this->getActiveQuoteItem();
		
		$bulkOptionHelper = Mage::helper('halox_bulkorder');

		if($quoteItem){
			//$bulkOption = $quoteItem->getOptionByCode('halox_bulk_buyRequest');
			$bulkOption = $bulkOptionHelper->getBulkRequestOptionByQuoteItem($quoteItem);
			if(is_array($bulkOption) && count($bulkOption) > 0){
				$preConfiguredData = $bulkOption;
				unset($preConfiguredData['super_attribute']);
			}
			
			$preConfiguredData['configureId'] = $quoteItem->getId();
		}

		return $this->helper('core')->jsonEncode($preConfiguredData); 
	}

}
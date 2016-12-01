<?php
/**
 * horizontal tab render block
 */
class Halox_BulkOrder_Block_Tab_Type_Horizontal extends Halox_BulkOrder_Block_Tab_Type_Abstract
{

	/**
	 * get current active quote item that is being configured
	 */
	protected $_activeQuoteItem;

	public function _construct()
	{
		$configureId = (int) Mage::app()->getRequest()->getParam('configure_id');
		if($configureId){
			$this->_activeQuoteItem = Mage::getSingleton('checkout/cart')
				->getQuote()->getItemById($configureId);
		}

		$baseCategoryId = (int) Mage::app()->getRequest()->getParam('base_id');
		if($baseCategoryId){
			$this->_baseCategoryId = $baseCategoryId;
		}

		return parent::_construct();

	}

	/**
	 * get current active quote item that is being configured
	 */
	public function getActiveQuoteItem()
	{
		return $this->_activeQuoteItem ? $this->_activeQuoteItem : null;
	}

	/**
	 * get base category id for the bulk order form
	 */
	public function getBaseCategoryId()
	{
		return $this->_baseCategoryId ? $this->_baseCategoryId : null;
	}

	/**
	 * get current active category
	 */
	public function getCurrentEntity()
	{

		if( ! $this->_currentEntity){
			$this->_currentEntity = Mage::getModel('catalog/category')->getCollection()
				->addIdFilter(array($this->getCurrentEntityId()))
				->addNameToResult()
				->setCurPage(1)
				->setPageSize(1)
				->getFirstItem();
		}

		return $this->_currentEntity;
	}

	/**
	 * get all category products to render the vertical tabs
	 */
	public function getChildren($category, $columns = array())
	{
		$collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter($category)
            ->addAttributeToFilter('status', array(
            	'eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED
            ))
            ->addStoreFilter();

        Mage::getSingleton('cataloginventory/stock')
        	->addInStockFilterToCollection($collection);    

        Mage::getSingleton('catalog/product_visibility')
        	->addVisibleInCatalogFilterToCollection($collection);

        if( ! empty($columns)){
			$collection->addAttributeToSelect($columns);
		}


		return $collection;
	}

	/**
	 * get the bulk order grid loader image url
	 */
	public function getLoaderImageUrl()
	{
		return $this->getSkinUrl('images/halox/bulkorder/loader.gif');
	}

	/**
	 * url to load the vertical tabs content
	 */
	public function getTabLoadUrl($product, $tabType)
	{
		$url = $this->getTabContentLoadUrl();

		$url = $url . 'id/' . $product->getId() 
					. '/base_id/' . $this->getBaseCategoryId()
					. '/type/' . $tabType 
					. '/parent/' . $this->getCurrentEntity()->getId();
		
		return $this->getActiveQuoteItem() 
			? $url . '/configure_id/' . $this->getActiveQuoteItem()->getId()
			: $url;
	}

	
}
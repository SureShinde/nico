<?php 
/**
 * vertical tabs block instance
 */
 class Halox_BulkOrder_Block_Tab_Type_Vertical extends Halox_BulkOrder_Block_Tab_Type_Abstract
 {

 	protected $_currentParent;

 	/**
 	 * currently opened parent tab id(horizontal tab)
 	 */
 	public function getCurrentParentId()
	{
		return Mage::registry('parent_entity_id');
	}

	public function getCurrentParent()
	{

		if( ! $this->_currentParent){
			$this->_currentParent = Mage::getModel('catalog/category')->getCollection()
				->addIdFilter(array($this->getCurrentParentId()))
				->setCurPage(1)
				->setPageSize(1)
				->getFirstItem();
		}

		return $this->_currentParent;
	}
	
	public function getCurrentEntity()
	{

		if( ! $this->_currentEntity){
			$this->_currentEntity = Mage::getModel('catalog/product')->getCollection()
				->addIdFilter(array($this->getCurrentEntityId()))
				->addAttributeToSelect(array('msrp'))
				->addFinalPrice()
				->setCurPage(1)
				->setPageSize(1)
				->getFirstItem();
		}

		return $this->_currentEntity;
	}

	/**
	 * get all category products to render the vertical tabs
	 */
	public function getChildren($product, $columns = array())
	{
		$collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addAttributeToFilter('id', $product->getId());

        if( ! empty($columns)){
			$collection->addAttributeToSelect($columns);
		}


		return $collection;
	}

	/**
	 * get the grid based on the current active tab category and product tab
	 */
	public function getProductGridHtml($category, $product)
	{
		$gridBlock = $this->getChild('bulk.order.grid');
		$gridBlock->addData(array(
			'category' => $category,
			'product' => $product
		));

		return $gridBlock->toHtml();
	}

}
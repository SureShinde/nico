<?php

/**
 * list first level sub-categories of a parent category similar to product list page(Hardware Landing Page Layout)
 */
 class Mage_Catalog_Block_Category_List extends Mage_Core_Block_Template
 {

 	protected $_category;

 	protected function _construct()
 	{
 		
 		parent::_construct();

	}

	public function getCurrentCategory()
	{
		if( ! $this->_category){
			$this->_category = Mage::registry('current_category');
		}

		return $this->_category;
	}

	/**
	 * @return first level child categories 
	 */
	public function getChildItems()
	{
		$childrenIds = $this->getCurrentCategory()->getChildren();

		$childCollection = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect(array('thumbnail', 'description', 'meta_title'))
			->addIdFilter($childrenIds)
			->addIsActiveFilter()
			->addAttributeToSort('position', 'asc')
			->addNameToResult();
            
		return $childCollection;
	}

	/**
	 * @return thumbnail url for category 
	 */
	public function getThumbnailImageUrl($item)
	{
		$url = false;
        if ($image = $item->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;
	}

 }

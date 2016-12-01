<?php

class Halox_BundleGrid_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option 
	extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
{

	public function __construct()
	{
		$this->setTemplate('halox/bundleGrid/product/edit/bundle/option.phtml');
		
		$this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
	}

}
<?php

class Halox_BundleGrid_Block_Catalog_Product_View_Type_Bundle_Option_Grid
	extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{

	/**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('halox/bundleGrid/catalog/product/view/type/bundle/option/grid.phtml');
    }

}
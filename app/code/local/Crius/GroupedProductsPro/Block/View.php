<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_GroupedProductsPro
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
/**
 * Grouped product view block
 */
class Crius_GroupedProductsPro_Block_View extends Mage_Catalog_Block_Product_View_Type_Grouped
{
    /**
     * Array of attributes used for the table
     *
     * @var array
     */
    protected $_tableAttributes = null;
    
    /**
     * Get attributes for the product table
     *
     * @return array
     */
    public function getTableAttributes()
    {
        if (!$this->_tableAttributes) {
            $this->_tableAttributes = array();
            // Collect relevant attributes from products
            foreach ($this->getAssociatedProducts() as $item) {
                foreach ($item->getAttributes() as $attribute) {
                    // Add attribute to table if it is an allowed attribute and if product has attribute data
                    if ($attribute->getIsVisibleInGroupedTable() && $item->getData($attribute->getAttributeCode()) && ($attribute->getFrontendInput() != 'media_image' || $item->getData($attribute->getAttributeCode()) != 'no_selection')) {
                        // Use attribute code as key to avoid duplicates
                        $this->_tableAttributes[$attribute->getAttributeCode()] = $attribute;
                    }
                }
            }
            // Remove keys and sort attributes
            $this->_tableAttributes = array_values($this->_tableAttributes);
			usort($this->_tableAttributes, array('Crius_GroupedProductsPro_Block_View', 'compareAttributes'));
        }
        return $this->_tableAttributes;
    }
    
    /**
     * Comparison function for sorting attributes
     *
     * @param $attr1 First attribute
     * @param $attr2 Second attribute
     * @return int
     */
    public static function compareAttributes($attr1, $attr2)
    {
        if ($attr1->getGroupedTableSortOrder() == $attr2->getGroupedTableSortOrder()) {
            return 0;
        }
        return ($attr1->getGroupedTableSortOrder() > $attr2->getGroupedTableSortOrder()) ? 1 : -1;
    }
    
    /**
     * Show add to cart buttons for each row?
     *
     * @return boolean
     */
    public function showAddToCartForRows()
    {
        return $this->_getConfigValue('add_row_to_cart');
    }
    
    /**
     * Show add to cart button in footer?
     *
     * @return boolean
     */
    public function showAddToCartInFooter()
    {
        return $this->_getConfigValue('add_all_to_cart');
    }
    
    /**
     * Show quantity fields in rows?
     *
     * @return boolean
     */
    public function showQuantityFields()
    {
        return $this->_getConfigValue('show_quantity');
    }
    
    /**
     * Get image width
     *
     * @return int
     */
    public function getImageWidth()
    {
        return Mage::getStoreConfig('groupedproductspro/settings/image_width');
    }
    
    /**
     * Get image height
     *
     * @return int
     */
    public function getImageHeight()
    {
        return Mage::getStoreConfig('groupedproductspro/settings/image_height');
    }
    
    /**
     * Get configuration value for key. First priority = product settings, second priority = system configuration
     *
     * @param string $key
     * @return mixed
     */
    protected function _getConfigValue($key)
    {
        // Return general configuration value if product value is not set
        $configvalue = Mage::getStoreConfig('groupedproductspro/settings/'.$key);
        $productconfigvalue = $this->getProduct()->getData('gpp_'.$key);
        switch ($productconfigvalue) {
            case 1:
                return true;
            case 2:
                return false;
            default:
                return $configvalue;
        }
    }
}
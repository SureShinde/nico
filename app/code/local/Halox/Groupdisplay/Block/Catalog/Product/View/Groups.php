<?php
/**
 * Get current product on product detail page
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Block_Catalog_Product_View_Groups extends Mage_Core_Block_Template
{
    protected $_product = null;

   public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }

}

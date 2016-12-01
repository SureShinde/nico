<?php
/**
 * @rewrite Mage_SalesRule_Model_Rule_Product
 * resolve dependency with Amasty_Promo extension
 */

//resolve module dependency with Amasty Promo Module
if(! Mage::helper('halox_dynamicSku')->isModuleEnabled('Amasty_Promo')){
    
    class_alias('Mage_SalesRule_Model_Rule_Condition_Product', 'RULE_CONDITION_PRODUCT_CLASS');
}else{
    class_alias('Amasty_Promo_Model_SalesRule_Rule_Condition_Product', 'RULE_CONDITION_PRODUCT_CLASS');
}

class Halox_DynamicSku_Model_SalesRule_Rule_Condition_Product 
    extends RULE_CONDITION_PRODUCT_CLASS
{

    protected function _addSpecialAttributes(array &$attributes)
    {
        
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_dynamic_sku'] = Mage::helper('halox_dynamicSku')->__('Dynamic Child Sku');
    }

    protected function _isBundle($product)
    {
        return $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;        
    }

    /**
     * build dynamic sku for quote item using quote item option information
     */
    protected function _getDynamicSku($product)
    {   

        $skuParts = array();

        //only for bundle products
        if ($product->hasCustomOptions() && $this->_isBundle($product)) {
            $customOption = $product->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            if (!empty($selectionIds)) {
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
                foreach ($selections->getItems() as $selection) {
                    $skuParts[] = trim($selection->getSku());
                }
            }
        }

        return implode('-', $skuParts);

    }

    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product = false;
        
        if ($object->getProduct() instanceof Mage_Catalog_Model_Product) {
            $product = $object->getProduct();
        } else {
            $product = Mage::getModel('catalog/product')
                ->load($object->getProductId());
        }
        
        $product->setQuoteItemDynamicSku($this->_getDynamicSku($product));
        
        $object->setProduct($product);
        
        return parent::validate($object);
    }

}
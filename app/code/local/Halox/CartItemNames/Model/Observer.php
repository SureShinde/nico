<?php
class Halox_CartItemNames_Model_Observer{

	/**
	 * get quote items bundle options
	 */
	protected function _getBundleOptions($parentItem)
	{
		if($parentItem->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
			return array();
		}

		$typeInstance = $parentItem->getProduct()->getTypeInstance(true);

		$optionsQuoteItemOption = $parentItem->getOptionByCode('bundle_option_ids');
		
		$bundleOptionsIds = $optionsQuoteItemOption 
			? unserialize($optionsQuoteItemOption->getValue()) 
			: array();

		$bundleOptions = array();	

		if ($bundleOptionsIds) {
		    
		    $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $parentItem->getProduct());

		    // get and add bundle selections collection
		    $selectionsQuoteItemOption = $parentItem->getOptionByCode('bundle_selection_ids');

		    $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());

		    if (!empty($bundleSelectionIds)) {
		        $selectionsCollection = $typeInstance->getSelectionsByIds(
		            unserialize($selectionsQuoteItemOption->getValue()),
		            $parentItem->getProduct()
		        );

		        $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);

		    }
		}

		return $bundleOptions;

	}

	/**
	 * change cart item names to include their selected options as well
	 */
	public function onSalesQuoteItemSetProduct($observer)
	{
		
		$currentStore = Mage::app()->getStore()->getId();
		//check if extension is enabled for current store
		if( ! Mage::helper('halox_cartitemnames')->isModuleActive($currentStore)){
			return $this;
		}

		$item = $observer->getEvent()->getQuoteItem();
		
		$product = $observer->getEvent()->getProduct();

		switch($product->getTypeId()){
			
			case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
				$helper = Mage::helper('catalog/product_configuration');
				$options = $helper->getConfigurableOptions($item);
				
				if(count($options) <= 0){
		        	return $this;
		        }

		        $itemName = $product->getName();

		        foreach($options as $option){
		        	$itemName .= ' - ' . $option['value'];
		        }

		        $item->setName($itemName);
				
				break;

			case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
				
				//Make sure these quote items are child items
				if( ! $item->hasParentItemId()){
					return $this;
				}

				$parentItem = Mage::getSingleton('checkout/cart')->getQuote()
					->getItemById($item->getParentItemId());
				
				//Make sure these quote items are child of bundle item
				if(! $parentItem || $parentItem->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
					return $this;
				}

				$bundleOptions = $this->_getBundleOptions($parentItem);

				$itemName = trim($parentItem->getProduct()->getName());
				if(count($bundleOptions) > 0){

            		$itemSelectionId = $item->getOptionByCode('selection_id')->getValue();
		            foreach($bundleOptions as $option){
		                foreach($option->getSelections() as $selection){
							if($selection->getProductId() == $item->getProduct()->getId()
		                        && $itemSelectionId == $selection->getSelectionId()
		                	){
		                        $itemName .= ' | ' . trim($option->getTitle()) . ' - ';
		                    	break;
		                    }
		                }
		            }
        
				}

				$itemName .= $item->getName();
		        
		        $item->setName($itemName);

		        break;
		}
		
		return $this;
		
	}
}
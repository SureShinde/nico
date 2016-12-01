<?php

class Halox_BundleGrid_Model_Observer extends Varien_Object
{
	
	protected function _checkProductForAllGridOptions($product)
	{
		$optionsCollection = Mage::getModel('bundle/option')->getResourceCollection()
                ->setProductIdFilter($product->getId())
                ->setPositionOrder()
                ->joinValues(Mage::app()->getStore()->getId());

        $isAllOptionsGrid = true;
        foreach($optionsCollection->getItems() as $option){
        	if($option->getType() != 'grid'){
        		$isAllOptionsGrid = false;
        		break;
        	}
        }        
        
        if($isAllOptionsGrid){
        	$product->setData('halox_all_grid_options', 1);
        }
	}


	public function onProductView($observer)
	{
		//check if extension is enabled for current store
		if( ! Mage::helper('halox_bundleGrid')->isModuleActive()){
			return $this;
		}
		
		$product = $observer->getEvent()->getProduct();

		if($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
			return $this;
		}

		$this->_checkProductForAllGridOptions($product);

		$currentAction = Mage::app()->getFrontController()->getAction();
		
		$isProductViewPage = $currentAction->getFullActionName() == 'catalog_product_view'; 


		//if current product has been added to cart already redirect 
		//to update product configuration page
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if($quote && $isProductViewPage){
        	$quoteItemCollection = $quote->getItemsCollection()
	        	->addFieldToFilter('product_id', array('eq' => $product->getId()))
	        	->addFieldToFilter('parent_item_id', array('null' => true));

	        if($quoteItemCollection->getSize() <= 0){
	        	return $this;
	        }

	        $configurCartUrl = Mage::getUrl('checkout/cart/configure',array(
	        	'id' => $quoteItemCollection->getFirstItem()->getId()
	        ));

	        Mage::app()->getResponse()->setRedirect($configurCartUrl)->sendResponse();

	        return $this;
        }

        
		return $this;

	}

}
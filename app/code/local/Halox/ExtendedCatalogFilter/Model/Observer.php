<?php

class Halox_ExtendedCatalogFilter_Model_Observer extends Varien_Object
{

	protected function _getHelper()
	{
		return Mage::helper('halox_extendedcatalogfilter');
	}

	protected function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * show extended catalog to only logged in privileged customers
	 * 
	 */
	public function catalogProductCollectionLoadAfter($observer)
	{
		if(Mage::app()->getStore()->isAdmin()){
			return $this;
		}	

		$storeId = Mage::app()->getStore()->getId();

		if(! $this->_getHelper()->isModuleActive($storeId)){
			return $this;
		}

		$collection = $observer->getEvent()->getCollection();

		$canShowCatalogToCustomer = false;

		if($this->_getCustomerSession()->isLoggedIn()){
			$canShowCatalogToCustomer = $this->_getCustomerSession()->getCustomer()->getData(
				Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CUSTOMER_ENTITY
			);	
		}
		
		if( ! $canShowCatalogToCustomer){

			$collection->addAttributeToFilter(array(
					array(
						'attribute' => Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CATALOG_PRODUCT_ENTITY,
						'neq' => 1
					),
					array(
						'attribute' => Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CATALOG_PRODUCT_ENTITY,
						'null' => true
					)
				),
				null,
				'left'
			);
			
		}	
		
		return $this;
		
	}

	/**
	 * unload catalog product if it is part of the extended catalog and customer
	 * is not logged in or doesn't have the privilage to view the extended catalog
	 */
	public function catalogProductLoadAfter($observer)
	{
		//currently functionality is limited to frontend only
		if(Mage::app()->getStore()->isAdmin()){
			return $this;
		}	

		$storeId = Mage::app()->getStore()->getId();

		//check if module is enabled for current store scope
		if(! $this->_getHelper()->isModuleActive($storeId)){
			return $this;
		}

		$product = $observer->getProduct();

		$isExtendedCatalogProduct = $product->getResource()->getAttributeRawValue(
			$product->getId(), 
			Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CATALOG_PRODUCT_ENTITY,
			Mage::app()->getStore()
		);

		if(!$isExtendedCatalogProduct){
			return $this;
		}

		$canShowProductToCustomer = false;
		if($this->_getCustomerSession()->isLoggedIn()){
			$canShowProductToCustomer = $this->_getCustomerSession()->getCustomer()->getData(
				Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CUSTOMER_ENTITY
			);
		}

		if(! $canShowProductToCustomer){
			$product->setData(null)->setId(null);
		}

		return $this;
	}

}
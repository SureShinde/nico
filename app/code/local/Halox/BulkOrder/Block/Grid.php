<?php

/**
 * Incapsulates method needed for rendering the quick order grid
 * @category Block instance
 */
class Halox_BulkOrder_Block_Grid extends Mage_Catalog_Block_Product_View_Type_Configurable
{

	/**
	 * get quote item currently clicked from cart for editing with E-Liquid Order form
	 */
	protected $_activeQuoteItem;
	
	/**
	 * cache used config attributes
	 */
	protected $_usedConfigAttributes;

	/**
	 * cache base attribute to display on X-Axis
	 */
	protected $_baseAttributeAxisX;
	
	/**
	 * cache base attribute to display on X-Axis
	 */
	protected $_baseAttributeAxisY;

	/**
	 * cache quote items found which represent current grid cell simple products
	 */
	protected $_quoteItemsByCellProducts = array();

	/**
	 * colspan value for subtotal and qty total
	 */
	protected $_subTotalSpan = '';

	/**
	 * @return current store id
	 */
	public function getCurrentStoreId()
	{
		return Mage::app()->getStore()->getId();
	}

	/**
	 * @return current store id
	 */
	public function getCurrentBaseCategoryId()
	{
		return Mage::registry('current_base_category_id');
	}

	/**
	 * get admin selected base attribute for X axis
	 */
	public function getBaseAttributeCodeAxisX(){
		
		return $this->helper('halox_bulkorder')->getBaseAttributeAxisX();
	}

	/**
	 * get admin selected base attribute for Y axis
	 */
	public function getBaseAttributeCodeAxisY(){
		
		return $this->helper('halox_bulkorder')->getBaseAttributeAxisY();
	}

	/**
     * get used configurable product super attributes
     * @return array 
	 */
	public function getConfigurableAttributes()
	{
		if( ! $this->_usedConfigAttributes){
			
			//we always expect configurable products with two attributes only
			$this->_usedConfigAttributes = $this->getProduct()->getTypeInstance(true)
				->getConfigurableAttributeCollection($this->getProduct())
	            ->orderByPosition()
	            ->setCurPage(1)
	            ->setPageSize(2)	
	            ->load();
	        
	    }

		return $this->_usedConfigAttributes;
	}

	/**
	 * get X-axis attribute for grid
	 */
	public function getBaseAttributeAxisX()
	{
		
		if( ! $this->_baseAttributeAxisX){

			$configAttributes = $this->getConfigurableAttributes();
			
			$this->_baseAttributeAxisX = $configAttributes->getFirstItem();
			
		}

		return $this->_baseAttributeAxisX;
		
	}

	/**
	 * get Y-axis attribute for grid
	 */
	public function getBaseAttributeAxisY()
	{
		if( ! $this->_baseAttributeAxisY){
			
			$configAttributes = $this->getConfigurableAttributes();
			
			$this->_baseAttributeAxisY = $configAttributes->getLastItem();	
			
		}

		return $this->_baseAttributeAxisY;
	}


	/**
	 * get used attribute options by attribute for current configurable product
	 */
	protected function _getUsedOptionsByAttribute($productAttribute)
	{
		$usedAttributeOptions = array();

		$options = $productAttribute->getFrontend()->getSelectOptions();

        $optionsByValue = array();
        foreach ($options as $option) {
            $optionsByValue[$option['value']] = $option['label'];
        }

        
        $associatedProducts = $this->getProduct()->getTypeInstance(true)
            ->getUsedProducts(array(
            	$productAttribute->getAttributeCode()
        	), 
            $this->getProduct()
        );
        
        foreach ($associatedProducts as $associatedProduct) {

            $optionValue = $associatedProduct->getData($productAttribute->getAttributeCode());

            if (array_key_exists($optionValue, $optionsByValue)) {
                // If option available in associated product
                if (!isset($values[$optionValue])) {
                    // If option not added, we will add it.
                    $usedAttributeOptions[$optionValue] = array(
                        'value_index'                => $optionValue,
                        'store_label'                => $optionsByValue[$optionValue],
                    );
                }
            }
        }

        
		usort($usedAttributeOptions, function ($a, $b) {
		    return strnatcmp($a['store_label'], $b['store_label']);
		});

		return $usedAttributeOptions;
	}



	/**
	 * @return used super attribute options sorted
	 */
	public function getAttributeOptionsAxisX()
	{
		

		$productAttribute = $this->getBaseAttributeAxisX()->getProductAttribute();
		
		$xAxisAttributeOptions = $this->_getUsedOptionsByAttribute($productAttribute);

		$xAxisAttributeOptions = array_merge(array(
				array('store_label' => $productAttribute->getStoreLabel($this->getCurrentStoreId()))
			), 
			$xAxisAttributeOptions
		);

		return $xAxisAttributeOptions;
	}

	/**
	 * @return used super attribute options sorted
	 */
	public function getAttributeOptionsAxisY()
	{
		$productAttribute = $this->getBaseAttributeAxisY()->getProductAttribute();
		
		$yAxisAttributeOptions = $this->_getUsedOptionsByAttribute($productAttribute);
		
		usort($yAxisAttributeOptions, function ($a, $b) {
		    return strnatcmp($a['store_label'], $b['store_label']);
		});

		return $yAxisAttributeOptions;
	}

	/**
	 * get simple product representing the order combinations
	 * @return Mage_Catalog_Model_Product instance
	 */
	public function getProductByCellOptions($optionX, $optionY, $columns = array())
	{
		
		$product = $this->getProduct();

		$cellAttributes = array(
			$this->getBaseAttributeAxisX()->getProductAttribute()->getId() => $optionX['value_index'],
			$this->getBaseAttributeAxisY()->getProductAttribute()->getId() => $optionY['value_index']
		);

		if(empty($columns)){
			$columns = 'name';
		}

		$productCollection = $product->getTypeInstance(true)->getUsedProductCollection($product)
			->addAttributeToSelect($columns)
			->addFinalPrice();
			
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($productCollection);
        }

		foreach($cellAttributes as $attrId => $value){
			$productCollection->addAttributeToFilter($attrId, $value);
		}

		$productCollection->setCurPage(1)->setPageSize(1);

		return $productCollection->getFirstItem();
	}

	/**
     * @return configurable product price as row base price
     */
	public function getBaseRowPrice()
	{
		return $this->getProduct()->getFinalPrice();
	}

	/**
     * @return configurable product msrp as row base msrp
     */
	public function getBaseRowMsrp()
	{
		return $this->getProduct()->getMsrp();
	}

	/**
	 * @return get subtotal colspan for a configurable product
	 */
	public function getSubtotalColspan()
	{
		if(!$this->_subTotalSpan){
			$this->_subTotalSpan = count($this->getAttributeOptionsAxisX()) + 3;
		}
		return $this->_subTotalSpan;
	}

	/**
	 * @return get subtotal colspan for a configurable product
	 */
	public function getQtyColspan()
	{
		return $this->getSubtotalColspan();
	}

	/**
	 * get item to be configured(clicked from cart)
	 */
	public function getActiveQuoteItem()
	{
		if( ! $this->_activeQuoteItem){
			$quoteItemId = (int) Mage::app()->getRequest()->getParam('configure_id');
			if($quoteItemId){
				$this->_activeQuoteItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($quoteItemId); 
			}	
		}

		return $this->_activeQuoteItem;
		
	}

	/**
	 * get quote item that represents the cell product instance in the cart
	 */
	public function getQuoteItemByCell($cellProduct)
	{
		if( ! $this->getActiveQuoteItem()){
			return null;
		}

		if( ! isset($this->_quoteItemsByCellProducts[$cellProduct->getId()])){

			$currentQuoteItemCollection = Mage::getSingleton('checkout/cart')->getQuote()
				->getAllVisibleItems();

		    $bulkOrderHelper = Mage::helper('halox_bulkorder');		
					
			foreach($currentQuoteItemCollection as $item){
				
				if( $item->getProductType() != Mage_Catalog_Model_Product_TYPE::TYPE_CONFIGURABLE){
					continue;
				}

				$simpleProductOption = $item->getOptionByCode('simple_product');
				if( ! $simpleProductOption){
					continue;
				}

				//$bulkRequestOption = $item->getOptionByCode('halox_bulk_buyRequest');
				$bulkRequestOptionValue = $bulkOrderHelper->getBulkRequestOptionByQuoteItem($item);
				
				if(empty($bulkRequestOptionValue)){
					continue;
				}

				//$bulkRequestOptionValue = unserialize($bulkRequestOption->getValue());
				
				if(! isset($bulkRequestOptionValue['base_category_id']) 
					|| ($bulkRequestOptionValue['base_category_id'] != $this->getCurrentBaseCategoryId())
				){
					continue;
				}

				if($simpleProductOption->getValue() == $cellProduct->getId()){
					$this->_quoteItemsByCellProducts[$cellProduct->getId()] = $item;
					
				}
			}

		}

		return isset($this->_quoteItemsByCellProducts[$cellProduct->getId()]) 
			? $this->_quoteItemsByCellProducts[$cellProduct->getId()] : null;
	}

	/**
	 * whether the bulk order grid is in configurable mode
	 */
	public function isQuoteItemToConfigure($product)
	{
		if($this->getQuoteItemByCell($product)){
			return true;
		}	

		return false;
	}

}

<?php 

/**
 * Custom block to add few methods to addtocart template for bundle products
 */
class Halox_BundleGrid_Block_Catalog_Product_View_Addtocart 
	extends Mage_Catalog_Block_Product_View
{
	
	protected $_hasAllGridOptions;

	public function _construct()
	{
		return parent::_construct();
	}

	/** 
	 * Check if product type is BUNDLE
	 */
	public function isBundle($product)
	{
		return $product->getTypeId() == 'bundle';
	}

	/**
     * check of this bundle product has all grid options
     */
    public function hasAllGridOptions()
    {
        	
        if(is_null($this->_hasAllGridOptions)){
            $bundleInfoBlockInstance = $this->getLayout()->getBlock('product.info.bundle');
            if($bundleInfoBlockInstance){
                $this->_hasAllGridOptions = true;
                $options = $bundleInfoBlockInstance->getOptions();
                foreach($options as $option){
                    if( $option->getType() != 'grid'){
                        $this->_hasAllGridOptions = false;
                        break;
                    }
                }
            }
        }

        return $this->_hasAllGridOptions;
    }

}  



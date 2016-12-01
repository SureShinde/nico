<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Excisetax extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public static $customColumns = false;
    public $file;
    const TWO_POINT_FOUR = 0.18;
    const FORTY_TWO = 3.15;
    const FIFTEEN = 1.125;
    const THIRTY = 2.25;
    const SEVEN = 0.525;
    const FOUR = 0.3;
    const TEN = 0.75;

    public function __construct($file = null) {
        $this->file = $file;
    }
    
    
    public function render(Varien_Object $row) {
	    $state = $row->getdata('region');
			if($state == "Pennsylvania"){
			   $exciseTax = $row->getdata('base_extra_tax_rule_amount');
			   return number_format((float)$exciseTax, 2, '.', '');
	        }
        if (empty(self::$customColumns) && ($this->file == false)) {
            self::$customColumns = true;
            return $row->getData('extra_tax_rule_amount');
        }
        
        if(($row->getData('increment_id')=='Totals') && ($this->file == true)){
            return $row->getData('extra_tax_rule_amount');
        }
        
        $itemId = $row->getData($this->getColumn()->getIndex());
        
        if (isset($itemId) && !empty($itemId)) {
            $orderItem = Mage::getModel('sales/order_item')->load($itemId);
            $orderItemArr = $orderItem ->getData();
            
            if(isset($orderItemArr) && !empty($orderItemArr)){
             $productId = $orderItemArr['product_id'];
             $sku = $orderItemArr['sku'];
         }
            
         if($orderItemArr['product_type'] == 'configurable'){
             $_product = Mage::getModel('catalog/product')
                ->loadByAttribute('sku',$sku);
             $eMl = $_product->getAttributeText('eliquid_ml');
         }else{
             
            $_product = Mage::getModel('catalog/product')
                    ->load($productId);
            $eMl = $_product->getAttributeText('eliquid_ml');
         }
          
            
            if (isset($eMl) && !empty($eMl)) {
              if ($eMl == 2.4) {
                    $rate = self::TWO_POINT_FOUR;
                } elseif ($eMl == 42) {
                    $rate = self::FORTY_TWO;
                } elseif ($eMl == 15) {
                    $rate = self::FIFTEEN;
                } elseif ($eMl == 30) {
                    $rate = self::THIRTY;
                } elseif ($eMl == 7) {
                    $rate = self::SEVEN;
                } elseif ($eMl == 4) {
                    $rate = self::FOUR;
                } elseif ($eMl == 10) {
                    $rate = self::TEN;
                } else {
                    $rate = 0;
                }
				
            $quantity = $row ->getdata('qty_ordered');
            $extax = ($rate * $quantity);
            if(isset($extax)){
                    $exciseTax = number_format((float)$extax, 2, '.', '');
                    return $exciseTax;
            }	
            }
        }

        
        
        return "-";
    }

}

?>
<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Rate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    const TWO_POINT_FOUR = 0.18;
    const FORTY_TWO = 3.15;
    const FIFTEEN = 1.125;
    const THIRTY = 2.25;
    const SEVEN = 0.525;
    const FOUR = 0.3;
    const TEN = 0.75;

    public function render(Varien_Object $row) {
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
		     $eMl = 0;
			 if(is_object($_product)){
             $eMl = $_product->getAttributeText('eliquid_ml');
			 }
         }else{
            
            $_product = Mage::getModel('catalog/product')
                    ->load($productId);
            $eMl = $_product->getAttributeText('eliquid_ml');
            
         }
            
            if (isset($eMl) && !empty($eMl)) {

                if ($eMl == 2.4) {
                    return self::TWO_POINT_FOUR;
                } elseif ($eMl == 42) {
                    return self::FORTY_TWO;
                } elseif ($eMl == 15) {
                    return self::FIFTEEN;
                } elseif ($eMl == 30) {
                    return self::THIRTY;
                } elseif ($eMl == 7) {
                    return self::SEVEN;
                } elseif ($eMl == 4) {
                    return self::FOUR;
                } elseif ($eMl == 10) {
                    return self::TEN;
                } else {
                    return "-";
                }
            }
        }

        return "-";
    }

}

?>
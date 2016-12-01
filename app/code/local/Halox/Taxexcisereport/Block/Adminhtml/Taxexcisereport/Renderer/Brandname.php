<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Brandname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    public function render(Varien_Object $row) {
	 $itemId = $row->getData($this->getColumn()->getIndex());
	 if(isset($itemId) && !empty($itemId)){
         $orderItem = Mage::getModel('sales/order_item')->load($itemId);
         $orderItemArr = $orderItem ->getData();
         
         if(isset($orderItemArr['sku'])){
             $sku = $orderItemArr['sku'];
         }
         
         if(isset($orderItemArr['name'])){
             $name = $orderItemArr['name'];
         }
         
         $skuName = $sku.'------'.$name ;
         
         return $skuName; 
         
         }	 

        return "-";
    }

}

?>
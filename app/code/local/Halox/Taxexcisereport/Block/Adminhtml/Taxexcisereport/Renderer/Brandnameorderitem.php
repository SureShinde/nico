<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Brandnameorderitem extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    public function render(Varien_Object $row) {
	 $orderId = $row->getData($this->getColumn()->getIndex());
	 if(!empty($orderId)){
         $order = Mage::getModel('sales/order')->load($orderId);
         //$orderItemArr = $order->getData();
         $orderItems = $order->getAllVisibleItems();
		 $skus = '';
		 $names = '';
         foreach ($orderItems as $item) {
		      $names .= $item->getSku()."---".$item->getName()."(Qty-- ".round($item->getQtyOrdered(),2).")<br>";
			  
		 }        
         
         return $names; 
         
         }	 

        return "-";
    }

}

?>
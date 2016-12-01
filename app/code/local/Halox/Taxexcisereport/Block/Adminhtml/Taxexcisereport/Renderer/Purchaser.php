<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Purchaser extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    public function render(Varien_Object $row) {
	 $orderId = $row->getData($this->getColumn()->getIndex());
	 if(isset($orderId) && !empty($orderId)){
             $order = Mage::getModel('sales/order')->load($orderId);
            $customerName = $order->getBillingAddress()->getName();
            
            return $customerName;
         }	 

        return "-";
    }

}

?>
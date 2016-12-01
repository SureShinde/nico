<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Purchaseraddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    public function render(Varien_Object $row) {
	 $orderId = $row->getData($this->getColumn()->getIndex());
	 if(isset($orderId) && !empty($orderId)){
             $order = Mage::getModel('sales/order')->load($orderId);
             $customerAddress = $order->getBillingAddress();
             $customerAddressArr = $customerAddress->getData();
             if(isset($customerAddressArr) && !empty($customerAddressArr)){
               $addressString = "";
               $street = $customerAddressArr['street'];  
               $city = $customerAddressArr['city'];  
               $region = $customerAddressArr['region'];  
               $postCode = $customerAddressArr['postcode'];  
               if(isset($street)){
                   $addressString.= $street;
               }  
               if(isset($city)){
                   $addressString.= ','.$city;
               }  
               
               if(isset($region)){
                   $addressString.= ','.$region;
               }  
               if(isset($postCode)){
                   $addressString.= ','.$postCode;
               } 
               
               return $addressString;
             }
           
         }	 

        return "-";
    }

}

?>
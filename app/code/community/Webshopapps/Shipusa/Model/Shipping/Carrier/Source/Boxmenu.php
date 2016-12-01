<?php
class Webshopapps_Shipusa_Model_Shipping_Carrier_Source_Boxmenu
{
    public function toOptionArray()
    {
        $arr = Mage::getModel('boxmenu/boxmenu')->toOptionArray();
        array_unshift($arr, array('value'=>'', 'label'=>Mage::helper('shipping')->__('None')));
        return $arr;
    }
}

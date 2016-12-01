<?php

class Sunovisio_CustomerFlag_Model_Flags extends Varien_Object
{
    static public function toOptionArray()
    {
        $result = array(NULL => 'Select a flag');
        
        $collection = Mage::getModel('customerflag/flag')->getCollection()->addFieldToFilter('status',1);
        
        if (count($collection)) {
            foreach($collection as $flag) {
                $result [$flag->getId()] = $flag->getLabel();
            }
        }
        
        return $result;
    }
}
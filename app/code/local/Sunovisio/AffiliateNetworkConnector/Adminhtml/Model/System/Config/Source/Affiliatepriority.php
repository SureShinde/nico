<?php

class Sunovisio_AffiliateNetworkConnector_Adminhtml_Model_System_Config_Source_Affiliatepriority
{
    public function toOptionArray()
    {
        $options    = array(
            array(
                'value' => 'First',
                'label' => Mage::helper('adminhtml')->__('First Affiliate')
            ),
            array(
                'value' => 'Last',
                'label' => Mage::helper('adminhtml')->__('Last Affiliate')
            ),
        );
        return $options;
    }
}

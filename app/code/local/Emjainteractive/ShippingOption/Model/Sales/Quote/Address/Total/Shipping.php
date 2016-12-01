<?php

class Emjainteractive_ShippingOption_Model_Sales_Quote_Address_Total_Shipping
    extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        if ($address->getShippingMethod() == 'umosaco_umosaco') {
            if ($rate = $address->getShippingRateByCode('umosaco_umosaco')) {
                $shippingOptions = unserialize($address->getQuote()->getData('emjainteractive_shippingoption'));
                $shippingOptions = $shippingOptions['umosaco'];
                if (isset($shippingOptions['Shipping Method']) && isset($shippingOptions['Shipping Service'])) {
                    $address->setShippingDescription(
                        $rate->getCarrierTitle() .
                        " ({$shippingOptions['Shipping Method']} - {$shippingOptions['Shipping Service']})");
                }
            }
        }
        return $this;
    }
}
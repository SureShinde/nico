<?php

/**
 * @override Mage_Sales_Model_Quote_Address_Rate 
 * @method importShippingRates 
 */
class Halox_ExpediteShipping_Model_Sales_Quote_Address_Rate 
    extends Mage_Sales_Model_Quote_Address_Rate
{
    
    /**
     * save actual cost without handling fees to new column 'expedite_orig_cost' for later use
     */
    public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate)
    {
        parent::importShippingRate($rate);
        
        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Method) {
            $this->setExpediteOrigCost($rate->getCost());
        }
        return $this;
    }
    
}


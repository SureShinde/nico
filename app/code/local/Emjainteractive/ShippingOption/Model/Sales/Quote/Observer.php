<?php

class Emjainteractive_ShippingOption_Model_Sales_Quote_Observer
{
    const XML_SHIPPING_TOTAL_CLASS = 'global/sales/quote/totals/shipping/class';
    const NEW_SHIPPING_TOTAL_CLASS = 'emjainteractive_shippingoption/sales_quote_address_total_shipping';


    protected $_oldShippingTotalClassName = false;

    public function collectTotalsBefore($observer)
    {
        $quote = $observer->getQuote();
        /*
         * Custom Shipping Option
         */
        if ($quote->getShippingAddress()->getShippingMethod() == 'umosaco_umosaco') {
            $this->_oldShippingTotalClassName = (string)Mage::getConfig()->getNode(self::XML_SHIPPING_TOTAL_CLASS);
            Mage::getConfig()->setNode(
                self::XML_SHIPPING_TOTAL_CLASS,
                self::NEW_SHIPPING_TOTAL_CLASS
            );
        }

        return $this;
    }

    public function collectTotalsAfter($observer)
    {
        if ($this->_oldShippingTotalClassName) {
            Mage::getConfig()->setNode(
                self::XML_SHIPPING_TOTAL_CLASS,
                $this->_oldShippingTotalClassName,
                true
            );
            $this->_oldShippingTotalClassName = false;
        }

        return $this;
    }
}
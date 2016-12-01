<?php

class Halox_AgeVerification_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data) {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $quote = $this->getQuote();
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

        $payment = $quote->getPayment();
        $payment->importData($data);

        $quote->save();
        
        //conditional checks
        $isAgeVerifiedModuleEnable = Mage::helper('ageverification')->isAgeVerifiedEnable();
        
          if ($isAgeVerifiedModuleEnable) {
        $this->getCheckout()
                ->setStepData('payment', 'complete', true)
                ->setStepData('verify', 'allow', true);
          }else{
               $this->getCheckout()
                ->setStepData('payment', 'complete', true)
                ->setStepData('review', 'allow', true);
          }

        return array();
    }

    public function saveVerify($data) {

        $this->getCheckout()
                ->setStepData('verify', 'complete', true)
                ->setStepData('review', 'allow', true);

        return array();
    }

}

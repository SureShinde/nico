<?php

class Halox_AgeVerification_Model_Observer {

    /**
     * 
     * @param type $observer
     * @return \Halox_AgeVerification_Model_Observer
     * Method saves Age verification data for Unregisterd users and set user verified if user gets verified from API and set DOB as well 
     */
    public function setCustomerAgeVerificationData($observer) {
        $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
        $orderModel = Mage::getModel('sales/order');
        $customerId = $orderModel->getCollection()->addFieldToFilter('quote_id', $quote_id)->getLastItem()->getCustomerId();
        $quoteModel = Mage::getModel('sales/quote');
        $verificationData = unserialize($quoteModel->load($quote_id)->getVerificationData());
        if (!empty($verificationData) && isset($verificationData)) {
            $ageverificationModel = Mage::getModel('ageverification/ageverificationdetails');
            foreach ($verificationData as $key => $data) {
                $data['customer_id'] = $customerId;
                $ageverificationModel->setData($data)->save();

                //set verified status
                $isPassedFromAPI = Mage::helper('ageverification')->isPassedFromAPI($customerId);
                if ($isPassedFromAPI) {
                    //Mage::helper('ageverification')->setCustomerVerified($customerId);
                    Mage::helper('ageverification')->setCustomerDOB($data, $customerId, 'unregistered');
                }
            }
        }
        return $this;
    }

    public function setOrderStatus($observer) {
        if( ! Mage::helper('ageverification')->isAgeVerifiedEnable()){
            return $this;
        }
        $order = $observer->getEvent()->getPayment()->getOrder();
        $orderId = $order->getId();
        $quoteId = $order->getQuoteId();
        $verificationStep = Mage::helper('ageverification')->checkMaxVerificationStep($order);

        if(3==$verificationStep){
            $order->setVerificationStatus('Age Verification Pending')->save();
            if($order->canHold()){
                $order->hold();
            }
        }else{
            $order->setVerificationStatus('Age Verified')->save();
        }
        
        return $this;
    }
    
    public function updateTotals($observer) {
        
       $quote =  Mage::getSingleton('checkout/session')->getQuote();
       $quote->setTotalsCollectedFlag(false);
       $quote->collectTotals()->save();
       
    }
    

}

<?php
class TroopID_Connect_Model_Cart_Observer {

    public function clearAffiliation(Varien_Event_Observer $observer) {

        if (!Mage::helper("troopid_connect")->isOperational())
            return;
        $cart   = $observer->getEvent()->getCart();
        $value  = $cart->getQuote()->getTroopidUid();

        if (isset($value) && $cart->getItemsCount() == 0) {
            $cart->getQuote()->setTroopidUid(NULL);
            $cart->getQuote()->setTroopidScope(NULL);
            $cart->getQuote()->setTroopidAffiliation(NULL);
            $cart->getQuote()->save();
        }

    }
	 
	 public function updateAffiliation(Varien_Event_Observer $observer) {

        if (!Mage::helper("troopid_connect")->isOperational())
            return;

        $cart   = $observer->getEvent()->getCart();
        $value  = $cart->getQuote()->getTroopidUid();

        if (empty($value)) {
            $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                    $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                    $customer->load($customerId);
                    if($customer->getId()){     
                        $tropUid = $customer->getTroopidUid();
                        $troopScope = $customer->getTroopidScope();
                        $troopAff = $customer->getTroopidAffiliation();
                        if(!empty($tropUid)){
                        $cart->getQuote()->setTroopidUid($tropUid);
                        $cart->getQuote()->setTroopidScope($troopScope);
                        $cart->getQuote()->setTroopidAffiliation($troopAff);
                        $cart->getQuote()->save();
                        }
                     }
           
        }

    }

}
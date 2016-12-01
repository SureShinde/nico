<?php

class Halox_Registration_Model_Observer {

    /**
     * 
     * @param type $observer
     * @return \Halox_Registration_Model_Observer
     * Method checks the cart sub total should be greater than customer minimum order amount for wholesale website on one page checkout
     */
    public function checkMinimumOrderAmount($observer) {
        $storeId = Mage::app()->getStore()->getStoreId();
        if ($storeId == 1) {
            $minimumOrderAmount = Mage::getSingleton('customer/session')->getCustomer()->getData('minimum_order_amount');
            $cartSubTotal = Mage::getSingleton('checkout/session')->getQuote()->getSubtotal();
            if ($cartSubTotal < $minimumOrderAmount) {
               // $warning = Mage::helper('checkout')->__('This order does not currently meet the $' . $minimumOrderAmount . ' minimum amount. Please add additional items prior to checkout.');
               // Mage::getSingleton('core/session')->addError($warning);
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
                return;
            }
        }
    }

}

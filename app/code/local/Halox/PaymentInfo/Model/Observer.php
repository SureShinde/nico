<?php
/**
 * Halox
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Halox
 * @package     Halox_CheckoutComment
 * @copyright   Copyright (c) 2016 Halox. 
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Halox_PaymentInfo_Model_Observer
{
    public function saveComment($observer) {
            $isEnabled = Mage::getStoreConfig('pocomment/check/enabled', Mage::app()->getStore()->getStoreId());
            if($isEnabled != 1){
               return;
            }    
            $params = Mage::app()->getRequest()->getParams();
            if(isset($params['payment_comment'])){
            $paymentComment = $params['payment_comment'];
            $paymentComment = trim($paymentComment);

            if (!empty($paymentComment)) {
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $quote->setPaymentComment($paymentComment)->save();
                $order = $observer->getEvent()->getOrder();
                $order->setPaymentComment($paymentComment)->save();
                //$quoteId = $order->getQuoteId(); 
                
              }
            
            }

    }
}
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

class Halox_CheckoutComment_Model_Observer
{
    public function saveComment($observer) {
        
        $enable_comments = Mage::helper('checkoutcomment')->getConfig('enable_comment');

        if ($enable_comments)	{

            $orderComment = $observer->getEvent()->getRequest()->getPost('customer_notes');

            $orderComment = trim($orderComment);

            if ($orderComment != "") {
                // prepend a heading
                //$orderComment = Mage::helper('checkoutcomment')->__("Customer Order Comment:\n").$orderComment;
                
                $observer->getEvent()->getQuote()->getShippingAddress()->setCustomerNotes($orderComment)->save();
            }
        }

    }
}
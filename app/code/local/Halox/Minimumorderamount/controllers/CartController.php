<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'CartController.php');
class Halox_Minimumorderamount_CartController extends Mage_Checkout_CartController
{
    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
        
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

           $minimumOrderAmount = Mage::getSingleton('customer/session')->getCustomer()->getData('minimum_order_amount');
           $cartTotal = $this->_getQuote()->getBaseSubtotalWithDiscount();
           
           if( $cartTotal < $minimumOrderAmount ){
               $warning = Mage::helper('checkout')->__('This order does not currently meet the $'.$minimumOrderAmount.' minimum amount. Please add additional items prior to checkout.');
               $cart->getCheckoutSession()->addNotice($warning);
               Mage::getSingleton('checkout/session')->setDisableCheckoutButton(true);
            }else{
                Mage::getSingleton('checkout/session')->setDisableCheckoutButton(false);
            }
           
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
            ->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session')
            ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }
    
    
    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
		
        if ($this->_validateFormKey()) {
            $id = (int)$this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->_getCart()->removeItem($id)
                        ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('Cannot remove the item.'));
        }
		
		$url = Mage::getUrl('*/*');
		header("Location: $url");
       // $this->_redirectReferer(Mage::getUrl('*/*'));
    }
    
    
}

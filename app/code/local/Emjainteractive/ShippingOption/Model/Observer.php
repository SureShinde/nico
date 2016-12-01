<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

class Emjainteractive_ShippingOption_Model_Observer
{
    public function saveShippingMethod($observer)
    {
        $passedShippingMethod = Mage::app()->getRequest()->getPost('shipping_method');

        if ($passedShippingMethod == 'umosaco_umosaco') {
            $optionsCollection = Mage::getModel('emjainteractive_shippingoption/option')->getCollection()->load();

            $passedShippingOptions = Mage::app()->getRequest()->getPost('emjainteractive_shippingoption', array());
            if (isset($passedShippingOptions['umosaco'])) {
                $passedShippingOptions = $passedShippingOptions['umosaco'];
            }

            $error = array();
            
            foreach($optionsCollection as $option) {
                if (strpos($option->getApplyTo(), 'umosaco') !== false) {
                    $passedOptionValue = (isset($passedShippingOptions[$option->getCode()]))
                        ? trim($passedShippingOptions[$option->getCode()])
                        : '';
                    if ($option->getIsRequired() && (!$passedOptionValue || empty($passedOptionValue))) {
                        $error[] = $option->getLabel() . ' can not be empty.';
                    }
                }
            }

            if (!count($error)) {
                Mage::getSingleton('checkout/session')->getQuote()->setData(
                    'emjainteractive_shippingoption',
                    serialize(Mage::app()->getRequest()->getPost('emjainteractive_shippingoption', ''))
                );
            } else {
                $observer->getControllerAction()->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $error = array('error' => -1, 'message' => implode("\r\n",$error));
                $observer->getControllerAction()->getResponse()->setBody(Mage::helper('core')->jsonEncode($error));
            }
        } else {
            Mage::getSingleton('checkout/session')->getQuote()->setData('emjainteractive_shippingoption','');
        }

        return $this;

    }
    
    public function adminCreateOrder($observer)
    {
        if( $options = Mage::app()->getRequest()->getPost('emjainteractive_shippingoption') ) {
            Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->addData(array('emjainteractive_shippingoption' => serialize($options)));
        }
    }
}
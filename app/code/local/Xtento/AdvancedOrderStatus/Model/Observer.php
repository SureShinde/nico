<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2014-10-13T21:08:02+02:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Observer.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_Observer
{
    /*
     * Save set notifications when saving an order status using an observer
     */
    public function sales_order_status_save_after($observer)
    {
        if (Mage::helper('advancedorderstatus')->getModuleEnabled()) {
            $statusCode = Mage::app()->getRequest()->getParam('status', false);
            $storeNotifications = Mage::app()->getRequest()->getPost('store_notifications', false);
            if (!empty($storeNotifications) && !empty($statusCode)) {
                Mage::getResourceModel('advancedorderstatus/status_notification')->removeNotifications($statusCode);
                foreach ($storeNotifications as $storeId => $templateId) {
                    $notificationData = array(
                        'store_id' => $storeId,
                        'status_code' => $statusCode,
                        'template_id' => $templateId
                    );
                    Mage::getModel('advancedorderstatus/status_notification')->addData($notificationData)->save();
                }
            } else if (empty($storeNotifications) && !empty($statusCode)) {
                Mage::getResourceModel('advancedorderstatus/status_notification')->removeNotifications($statusCode);
            }
        }
    }

    /*
     * Alternative way of sending update emails
     */
    public function sales_order_save_after_status_change($observer)
    {
        #FB::log($observer->getEvent()->getOrder()->getOrigData('state').' - '.$observer->getEvent()->getOrder()->getState());
        #FB::log($observer->getEvent()->getOrder()->getOrigData('status').' - '.$observer->getEvent()->getOrder()->getStatus());
        #FB::log('-----------');
        return; // Disabled
        try {
            $event = $observer->getEvent();
            $order = $observer->getOrder();

            if (!Mage::helper('advancedorderstatus')->getModuleEnabled()) {
                return;
            }
            if ($order->getOrigData('status') == $order->getStatus()) {
                return;
            }
            $storeId = $order->getStoreId();
            $notificationCollection = Mage::getModel('advancedorderstatus/status_notification')->getCollection()
                ->addFieldToFilter('template_id', array('neq' => -1))
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('status_code', $order->getStatus());
            if ($notificationCollection->count() <= 0) {
                return;
            }
            if ($notificationCollection->getItemByColumnValue('store_id', $order->getStoreId())) {
                $templateId = $notificationCollection->getItemByColumnValue('store_id', $order->getStoreId())->getTemplateId();
                if ($templateId == 0) {
                    $templateId = 'advancedorderstatus_notification';
                }

                // Get the destination email addresses to send copies to
                $copyTo = Mage::getModel('sales/order')->_getEmails(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_COPY_TO);
                $copyMethod = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);

                // Retrieve customer name
                if ($order->getCustomerIsGuest()) {
                    $customerName = $order->getBillingAddress()->getName();
                } else {
                    $customerName = $order->getCustomerName();
                }

                $mailer = Mage::getModel('core/email_template_mailer');
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($order->getCustomerEmail(), $customerName);
                if ($copyTo && $copyMethod == 'bcc') {
                    // Add bcc to customer email
                    foreach ($copyTo as $email) {
                        $emailInfo->addBcc($email);
                    }
                }
                $mailer->addEmailInfo($emailInfo);

                // Email copies are sent as separated emails if their copy method is 'copy'
                if ($copyTo && $copyMethod == 'copy') {
                    foreach ($copyTo as $email) {
                        $emailInfo = Mage::getModel('core/email_info');
                        $emailInfo->addTo($email);
                        $mailer->addEmailInfo($emailInfo);
                    }
                }

                // Set all required params and send emails
                $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
                $mailer->setStoreId($storeId);
                $mailer->setTemplateId($templateId);
                $mailer->setTemplateParams(array(
                        'order' => $order,
                        'comment' => '',
                        'billing' => $order->getBillingAddress()
                    )
                );
                $mailer->send();
            }
            return;
        } catch (Exception $e) {
            Mage::log('Event handler exception for event sales_order_save_after_status_change: ' . $e->getMessage(), null, 'xtento_aos_event.log', true);
            return;
        }
    }

    /**
     * Add mass-actions to the sales order grid, the non-intrusive way.
     * @param type $observer
     */
    public function core_block_abstract_prepare_layout_after($observer)
    {
        $block = $observer->getEvent()->getBlock();
        #Mage::log('XTENTO - Controller name is: '.$block->getRequest()->getControllerName(), null, '', true);

        if (in_array($block->getRequest()->getControllerName(), $this->getControllerNames())) {
            $isSecure = Mage::app()->getStore()->isCurrentlySecure() ? true : false;
            if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction || $block instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction) {
                if (!$this->_initBlocks($block)) {
                    return;
                }
                $orderStatuses = Mage::getModel('advancedorderstatus/system_config_source_order_status')->toOptionArray();
                array_shift($orderStatuses);
                if (Mage::registry('moduleString') !== 'false') {
                    return;
                }
                $visibleOrderStatuses = Mage::getStoreConfig('advancedorderstatus/general/visible_order_statuses');
                $visibleOrderStatuses = explode(",", $visibleOrderStatuses);
                if (!empty($visibleOrderStatuses)) {
                    if (isset($visibleOrderStatuses[0]) && $visibleOrderStatuses[0] === '') {
                        # All statuses
                    } else {
                        foreach ($orderStatuses as $key => $orderStatus) {
                            if (!in_array($orderStatus['value'], $visibleOrderStatuses)) {
                                unset($orderStatuses[$key]);
                            }
                        }
                    }
                }
                //foreach ($orderStatuses as $)
                if ($this->_isAllowed()) {
                    $block->addItem('change_order_status', array(
                        'label' => Mage::helper('advancedorderstatus')->__('Change Order Status'),
                        'url' => Mage::app()->getStore()->getUrl('*/advancedorderstatus_grid/mass', array('action' => 'change_order_status', '_secure' => $isSecure)),
                        'additional' => array('order_status' => array(
                            'name' => 'new_order_status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('advancedorderstatus')->__('Order Status'),
                            'values' => $orderStatuses,
                        ))
                    ));
                }
            }
        }
    }

    private function _isAllowed()
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/advancedorderstatus/actions/change_status')) {
            return false;
        }
        return true;
    }

    private function _initBlocks($block)
    {
        if (!Mage::helper('advancedorderstatus')->getModuleEnabled()) {
            return false;
        }
        return true;
    }

    /*
     * Get controller names where the module is supposed to modify the block
     */
    public function getControllerNames()
    {
        return array('order', 'sales_order', 'adminhtml_sales_order', 'admin_sales_order', 'orderspro_order');
    }

    public function controller_action_predispatch_adminhtml($event)
    {
        // Check if this module was made for the edition (CE/PE/EE) it's being run in
        $controller = $event->getControllerAction();
        if ((in_array($controller->getRequest()->getControllerName(), $this->getControllerNames()) && $controller->getRequest()->getActionName() == 'index') || ($controller->getRequest()->getControllerName() == 'system_config' && $controller->getRequest()->getParam('section') == 'advancedorderstatus')) {
            if (!Mage::registry('edition_warning_shown_aos')) {
                if (Xtento_AdvancedOrderStatus_Helper_Data::EDITION !== 'CE' && Xtento_AdvancedOrderStatus_Helper_Data::EDITION !== '') {
                    if (Mage::helper('xtcore/utils')->getIsPEorEE() && Mage::helper('advancedorderstatus')->getModuleEnabled() && Xtento_AdvancedOrderStatus_Helper_Data::EDITION !== 'EE') {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtcore')->__('Attention: The installed Advanced Order Status version is not compatible with the Enterprise Edition of Magento. The compatibility of the currently installed extension version has only been confirmed with the Community Edition of Magento. Please go to <a href="https://www.xtento.com" target="_blank">www.xtento.com</a> to purchase or download the Enterprise Edition of this extension in our store if you\'ve already purchased it.'));
                    }
                }
                Mage::register('edition_warning_shown_aos', true);
            }
        }
    }
}
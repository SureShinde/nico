<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2013-06-17T17:38:03+02:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Processor.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_Processor extends Mage_Core_Model_Abstract
{
    private $_statuses;

    /*
    * Process orders
    */
    public function processOrders()
    {
        if (!Mage::helper('advancedorderstatus')->getModuleEnabled()) {
            return $this;
        }
        $orderIds = Mage::app()->getRequest()->getParam('order_ids');
        $newOrderStatus = Mage::app()->getRequest()->getParam('new_order_status');
        if (!is_array($orderIds) || empty($newOrderStatus)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedorderstatus')->__('Please select order(s) as well as a new status.'));
            return $this;
        }
        if (!Xtento_AdvancedOrderStatus_Model_System_Config_Source_Order_Status::isEnabled()) {
            return $this;
        }

        // Order status modifications
        if (!isset($this->_statuses)) {
            $this->_statuses = Mage::getSingleton('advancedorderstatus/system_config_source_order_status')->toArray();
        }

        $modifiedCount = 0;
        foreach ($orderIds as $orderId) {
            try {
                $isModified = false;

                $order = Mage::getModel('sales/order')->load($orderId);
                if (!$order || !$order->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError('Could not modify order with entity_id ' . $orderId . '. Order has been deleted in the meantime?');
                    continue;
                }

                $oldStatus = $order->getStatus();
                if (!empty($newOrderStatus)) {
                    #$this->_setOrderState($order, $newOrderStatus);
                    $order->setStatus($newOrderStatus)->save();
                    $isModified = true;
                }
                if ($oldStatus !== $order->getStatus()) {
                    if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                        $order->addStatusHistoryComment('', false)->setIsCustomerNotified(0);
                    } else {
                        // 1.3 compatibility
                        $order->addStatusToHistory($order->getStatus());
                    }

                    // Compatibility fix for Amasty_OrderStatus
                    $statusModel = Mage::registry('amorderstatus_history_status');
                    if (($statusModel && $statusModel->getNotifyByEmail()) || Mage::registry('advancedorderstatus_notifications')) {
                        $order->sendOrderUpdateEmail();
                    }
                    // End
                    $order->save();
                }

                if ($isModified) {
                    $modifiedCount++;
                }
                unset($order);
            } catch (Exception $e) {
                if (isset($order) && $order && $order->getIncrementId()) {
                    $orderId = $order->getIncrementId();
                }
                Mage::getSingleton('adminhtml/session')->addError('Exception (Order # ' . $orderId . '): ' . $e->getMessage());
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancedorderstatus')->__('Total of %d order(s) were modified.', $modifiedCount));

        return $this;
    }

    private function _setOrderState($order, $newOrderStatus)
    {
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            if (!isset($this->_orderStates)) {
                $this->_orderStates = Mage::getModel('sales/order_config')->getStates();
            }
            foreach ($this->_orderStates as $state => $label) {
                foreach (Mage::getModel('sales/order_config')->getStateStatuses($state, false) as $status) {
                    if ($status == $newOrderStatus) {
                        $order->setData('state', $state);
                        return;
                    }
                }
            }
        }
    }
}
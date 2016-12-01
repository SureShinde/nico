<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2013-11-19T16:19:32+01:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Sales/Order.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_Sales_Order extends TBT_Rewards_Model_Sales_Order
{
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $storeId = $this->getStore()->getId();

        // XTENTO Modification
        $notificationCollection = Mage::registry('advancedorderstatus_notifications');
        // XTENTO Modification
        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId) && $notificationCollection !== null) {
            return $this;
        }

        // XTENTO Modification
        if (Mage::registry('advancedorderstatus_notified_' . $this->getStatus() . $this->getId()) !== null) {
            return $this;
        } else {
            Mage::register('advancedorderstatus_notified_' . $this->getStatus() . $this->getId(), true, true);
        }
        #if ($notificationCollection) {
        #$notifyCustomer = true;
        #}
        // XTENTO Modification

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        // XTENTO Modification
        if ($notificationCollection && $notificationCollection->getItemByColumnValue('store_id', $storeId)) {
            $templateId = $notificationCollection->getItemByColumnValue('store_id', $storeId)->getTemplateId();
            if ($templateId == 0) {
                $templateId = 'advancedorderstatus_notification';
            }
        }
        // XTENTO Modification

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order' => $this,
                'comment' => $comment,
                'billing' => $this->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }

    public function addStatusHistoryComment($comment, $status = false)
    {
        try {
            if (Mage::helper('advancedorderstatus')->getModuleEnabled()) {
                // Are there any notifications that should be dispatched?
                $notificationCollection = Mage::getModel('advancedorderstatus/status_notification')->getCollection()
                    ->addFieldToFilter('template_id', array('neq' => -1))
                    ->addFieldToFilter('store_id', $this->getStore()->getId())
                    ->addFieldToFilter('status_code', $status ? $status : $this->getStatus());
                if ($notificationCollection->count() > 0) {
                    Mage::register('advancedorderstatus_notifications', $notificationCollection, true);

                    $isNotified = true;
                    $postData = Mage::app()->getRequest()->getPost('history');
                    if (!empty($postData)) {
                        $isNotified = isset($postData['is_customer_notified']) ? $postData['is_customer_notified'] : false;
                    }
                    Mage::register('advancedorderstatus_notified', $isNotified, true);

                    if (Mage::app()->getRequest()->getActionName() !== 'addComment') {
                        $this->sendOrderUpdateEmail();
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log('Exception in Xtento_AdvancedOrderStatus: ' . $e->getMessage(), null, 'xtento_exception.log', true);
        }

        return parent::addStatusHistoryComment($comment, $status);
    }
}
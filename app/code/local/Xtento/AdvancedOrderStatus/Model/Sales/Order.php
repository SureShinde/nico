<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2013-11-19T16:19:32+01:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Sales/Order.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
class Xtento_AdvancedOrderStatus_Model_Sales_Order extends Mage_Sales_Model_Order
{
    
    /**
     * HCL-190::this flag tracks the original status for the order object
     */
    protected $_originalStatus = '';

    protected function _afterLoad()
    {
        $this->_originalStatus = $this->getStatus();

        return parent::_afterLoad();
    }

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

        $customerNoteHtml = "";
        $paymentBlockHtml = "";

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
        //Code added by NicopureLab Team for wholesale site HCL-165 start
        if (1 == $storeId) {
            
            //HCL-190::check if a email template is assigned to current order status
            if($notificationCollection && $notificationCollection->getSize() > 0){

                //Mage::log('mail sent order status::' . $this->getStatus(), null, 'orderstatus.log', true);
                //code for wholesale
                $emailInfo = Mage::getModel('core/email_info');
                $recieverEmailId = Mage::getStoreConfig('trans_email/ident_custom1/email', $storeId);
                $recieverName = Mage::getStoreConfig('trans_email/ident_custom1/name', $storeId);
                if (isset($recieverEmailId) && !empty($recieverEmailId)) {
                    $emailInfo->addTo($recieverEmailId, $recieverName);
                }
                $mailer->addEmailInfo($emailInfo);    
            }
            
            //Code added by NicopureLab Team for wholesale site HCL-165 end
        } else {
            //code for retail
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
        }

        if (1 == $storeId) {

            //HCL-190::check if a email template is assigned to current order status
            if($notificationCollection && $notificationCollection->getSize() > 0){

                $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($storeId);
                $paymentBlockHtml = $paymentBlock->toHtml();

                //to remove the Purchase order number from order-email[start]	    
                $paymentMethod = $this->getPayment()->getData('method');
                if($paymentMethod =='purchaseorder'){
                        $subString = '<p>Purchase Order Number: </p>';
                        $paymentBlockHtml =str_replace($subString," ",$paymentBlockHtml);
                }
                //to remove the Purchase order number from order-email[end]
                
                $customerId = $this->getCustomerId();
                $customerNoteHtml = "";
                if (isset($customerId) && !empty($customerId)) {
                    $customerObj = Mage::getModel('customer/customer')
                            ->load($customerId);

                    $customerAdminNote = $customerObj->getData('admin_note');
                    if (isset($customerAdminNote) && !empty($customerAdminNote)) {
                        $customerNoteHtml = "<p style='color:#8c8c8c; margin:0;'>$customerAdminNote</p></br>";
                    }
                }    
            }
        }
        
        
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
            'order' => $this,
            'comment' => $comment,
            'payment_html' => $paymentBlockHtml,
            'customer_note_html' => $customerNoteHtml,
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

                //HCL-190::check if original status for the order has changed
                if ($notificationCollection->getSize() > 0 && $this->_originalStatus != $this->getStatus()) {
                    
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
                //HCL-190::unset flags in registry if not required
                }else{
                    Mage::unregister('advancedorderstatus_notifications');
                    Mage::unregister('advancedorderstatus_notified');
                }
            //HCL-190::unset flags in registry if not required    
            }else{
                Mage::unregister('advancedorderstatus_notifications');
                Mage::unregister('advancedorderstatus_notified');
            }
        } catch (Exception $e) {
            Mage::log('Exception in Xtento_AdvancedOrderStatus: ' . $e->getMessage(), null, 'xtento_exception.log', true);
        }

        return parent::addStatusHistoryComment($comment, $status);
    }
	
	public function queueNewOrderEmail($forceMode = false)
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
            
            //to remove the Purchase order number from order-email[start]	    
            $paymentMethod = $this->getPayment()->getData('method');
            if($paymentMethod =='purchaseorder'){
                    $subString = '<p><strong>Purchase Order Number:</strong> <span class="nobr"></span></p>';
                    $paymentBlockHtml =str_replace($subString," ",$paymentBlockHtml);
            }
            //to remove the Purchase order number from order-email[end]
            
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
            $customerId = $this->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $salesRep = $customer->getSalesRep();
            $salesRepEmail = '';
            if(!empty($salesRep)){
                $salesAgent = Mage::getModel('halox_salesagent/agent')->load($salesRep);
                $salesRepEmail = $salesAgent->getEmail();
			}
        }

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        if(!empty($salesRepEmail)){            
             $emailInfo->addBcc($salesRepEmail);
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
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
	
        
        $mailer->setTemplateParams(array(
            'order'        => $this,
            'billing'      => $this->getBillingAddress(),
            'payment_html' => $paymentBlockHtml
        ));

        /** @var $emailQueue Mage_Core_Model_Email_Queue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_NEW_ORDER)
            ->setIsForceCheck(!$forceMode);

        $mailer->setQueue($emailQueue)->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
}
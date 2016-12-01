<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2013-01-31T21:07:34+01:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Block/Adminhtml/Sales/Order/Status/New/Form.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Block_Adminhtml_Sales_Order_Status_New_Form extends Mage_Adminhtml_Block_Sales_Order_Status_New_Form
{

    /**
     * Prepare form fields and structure.. for notifications.
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        if (!Mage::helper('advancedorderstatus')->getModuleEnabled() || !Xtento_AdvancedOrderStatus_Model_System_Config_Source_Order_Status::isEnabled()) {
            return parent::_prepareForm();
        }
        parent::_prepareForm();

        $model = Mage::registry('current_status');
        if (!is_object($model)) {
            $notifications = array();
        } else {
            // Fetch notifications
            $notifications = Mage::getModel('advancedorderstatus/status_notification')->getNotifications($model->getStatus());
        }
        // Get form
        $form = $this->getForm();

        $fieldset = $form->addFieldset('store_notifications_fieldset', array(
            'legend' => Mage::helper('sales')->__('Order Status Notifications'),
            'table_class' => 'form-list stores-tree',
        ));

        // Fetch email templates, add "default" option
        $emailTemplates = Mage::getResourceModel('core/email_template_collection')->toOptionArray();
        array_unshift(
            $emailTemplates,
            array(
                'value' => '0',
                'label' => Mage::helper('advancedorderstatus')->__('Order Status Change - Default Template'),
            )
        );
        array_unshift(
            $emailTemplates,
            array(
                'value' => '-1',
                'label' => Mage::helper('advancedorderstatus')->__('--- No template selected - no notification ---'),
            )
        );

        // Output stores
        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_notification_label", 'note', array(
                'label' => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_notification_label", 'note', array(
                    'label' => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField("store_notification_{$store->getId()}", 'select', array(
                        'name' => 'store_notifications[' . $store->getId() . ']',
                        'required' => false,
                        'label' => $store->getName(),
                        'value' => isset($notifications[$store->getId()]) ? $notifications[$store->getId()] : '',
                        'values' => $emailTemplates,
                        'fieldset_html_class' => 'store',
                    ));
                }
            }
        }

        if ($model) {
            $form->addValues($model->getData());
        }
        #$form->setAction($this->getUrl('*/sales_order_status/save'));

        return $this;
    }

    public function prepareForm()
    {
        return self::_prepareForm();
    }
}

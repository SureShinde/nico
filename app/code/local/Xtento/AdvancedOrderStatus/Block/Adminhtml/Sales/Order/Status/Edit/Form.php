<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2012-12-28T16:15:06+01:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Block/Adminhtml/Sales/Order/Status/Edit/Form.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Block_Adminhtml_Sales_Order_Status_Edit_Form extends Xtento_AdvancedOrderStatus_Block_Adminhtml_Sales_Order_Status_New_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('new_order_status');
    }

    /**
     * Modify structure of new status form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        parent::prepareForm();
        $form = $this->getForm();
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('*/sales_order_status/save', array('status' => $this->getRequest()->getParam('status')))
        );
        return $this;
    }
}

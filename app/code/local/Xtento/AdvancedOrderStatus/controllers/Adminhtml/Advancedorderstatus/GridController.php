<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2013-06-11T12:28:21+02:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/controllers/Adminhtml/Advancedorderstatus/GridController.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Adminhtml_AdvancedOrderStatus_GridController extends Mage_Adminhtml_Controller_Action
{
    
	public function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('sales/order/advancedorderstatus/actions');
	}

    public function massAction()
    {
        Mage::getModel('advancedorderstatus/processor')->processOrders();
        $this->_redirect('adminhtml/sales_order');
    }
}
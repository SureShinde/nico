<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2014-09-08T15:37:13+02:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Sales/Order/Status/History.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_Sales_Order_Status_History extends Mage_Sales_Model_Order_Status_History
{
    public function setIsCustomerNotified($flag = null)
    {
        if (Mage::registry('advancedorderstatus_notifications') !== NULL && Mage::registry('advancedorderstatus_notified')) {
            $flag = 1;
        }
        return parent::setIsCustomerNotified($flag);
    }
}
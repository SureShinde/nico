<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2012-12-25T18:07:56+01:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/Mysql4/Status/Notification.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_Mysql4_Status_Notification extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('advancedorderstatus/status_notification', 'notification_id');
    }

    public function removeNotifications($statusCode)
    {
        Mage::getSingleton('core/resource')->getConnection('core_write')->query('DELETE FROM ' . $this->getTable('advancedorderstatus/status_notification') . ' WHERE status_code = "' . $statusCode . '"');
    }
}
<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (1.1.4)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:10:55+00:00
 * Last Modified: 2012-03-25T16:51:36+02:00
 * File:          app/code/local/Xtento/AdvancedOrderStatus/Model/System/Config/Backend/Import/Enabled.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_AdvancedOrderStatus_Model_System_Config_Backend_Import_Enabled extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        Mage::register('advancedorderstatus_modify_event', true, true);
        parent::_beforeSave();
    }

    public function has_value_for_configuration_changed($observer)
    {
        if (Mage::registry('advancedorderstatus_modify_event') == true) {
            Mage::unregister('advancedorderstatus_modify_event');
            Xtento_AdvancedOrderStatus_Model_System_Config_Source_Order_Status::isEnabled();
        }
    }
}

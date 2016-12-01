<?php

/**
 * Product:       Xtento_GridActions (1.7.2)
 * ID:            bFit8D9U13TLA9eMKO/BcMH3i/3sHORtmV6LD3+VLJY=
 * Packaged:      2013-09-29T19:45:02+00:00
 * Last Modified: 2011-12-11T17:24:51+01:00
 * File:          app/code/local/Xtento/GridActions/Model/System/Config/Source/Actions.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_GridActions_Model_System_Config_Source_Actions
{

    public function toOptionArray()
    {
        $actions = Mage::getModel('gridactions/system_config_source_order_actions')->getOrderActions();
        # Add your own actions:
        # $actions[] = array('value' => '__value__', 'label' => 'your_label');        
        
        return $actions;
    }

}

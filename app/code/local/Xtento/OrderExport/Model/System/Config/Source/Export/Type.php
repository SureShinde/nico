<?php

/**
 * Product:       Xtento_OrderExport (1.6.7)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:11:13+00:00
 * Last Modified: 2012-11-29T18:03:45+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Source/Export/Type.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Source_Export_Type
{

    public function toOptionArray()
    {
        return Mage::getSingleton('xtento_orderexport/export')->getExportTypes();
    }

}
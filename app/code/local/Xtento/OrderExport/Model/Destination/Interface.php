<?php

/**
 * Product:       Xtento_OrderExport (1.6.7)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:11:13+00:00
 * Last Modified: 2012-11-23T19:26:35+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Destination/Interface.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

interface Xtento_OrderExport_Model_Destination_Interface
{
    public function testConnection();
    public function saveFiles($fileArray);
}
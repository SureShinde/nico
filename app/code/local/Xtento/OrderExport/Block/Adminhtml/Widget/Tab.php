<?php

/**
 * Product:       Xtento_OrderExport (1.6.7)
 * ID:            jobgRKabxlPEzoqqkkBfLPfFVOiNLx18+EaehorlQWY=
 * Packaged:      2015-01-15T19:11:13+00:00
 * Last Modified: 2013-01-18T14:24:47+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Widget/Tab.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Widget_Tab extends Mage_Adminhtml_Block_Widget_Form
{
    protected function getFormMessages()
    {
        $formMessages = array();
        return $formMessages;
    }

    protected function _toHtml()
    {
        if ($this->getRequest()->getParam('ajax')) {
            return parent::_toHtml();
        }
        return $this->_getFormMessages() . parent::_toHtml();
    }

    protected function _getFormMessages()
    {
        $html = '<div id="messages"><ul class="messages">';
        foreach ($this->getFormMessages() as $formMessage) {
            $html .= '<li class="' . $formMessage['type'] . '-msg"><ul><li><span>' . $formMessage['message'] . '</span></li></ul></li>';
        }
        $html .= '</ul></div>';
        return $html;
    }
}
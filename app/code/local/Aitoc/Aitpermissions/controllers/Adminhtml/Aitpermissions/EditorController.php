<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.9.2
 * @license:     QvHpbEc0oOzK3qo2hlmyDcWVqjqnkaNA9m9UogV4wV
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Adminhtml_Aitpermissions_EditorController extends Mage_Adminhtml_Controller_Action
{
    public function attributegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_editor_attribute')->toHtml()
        );
    }
}
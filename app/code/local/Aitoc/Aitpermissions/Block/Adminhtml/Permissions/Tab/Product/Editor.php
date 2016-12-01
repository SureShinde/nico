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
class Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/product/editor.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('tabs', $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_editor_tabs', 'productEditorTabs'));
        $this->setChild('attribute', $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_editor_attribute', 'productEditorAttribute'));
        return $this;
    }
}
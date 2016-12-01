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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCmsBlockGrid extends Mage_Adminhtml_Block_Cms_Block_Grid
{
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
        $collection = Mage::getModel('cms/block')->getCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $collection->addStoreFilter($role->getAllowedStoreviewIds());
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}
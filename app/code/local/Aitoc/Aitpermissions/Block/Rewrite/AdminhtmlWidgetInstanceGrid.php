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
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlWidgetInstanceGrid
    extends Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
{
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Widget_Model_Mysql4_Widget_Instance_Collection */
        $collection = Mage::getModel('widget/widget_instance')->getCollection();
        $this->setCollection($collection);
        
        if ($this->getCollection())
        {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter))
            {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter))
            {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter))
            {
                $this->_setFilterValues($filter);
            }
            else if (0 !== sizeof($this->_defaultFilter))
            {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex())
            {
                $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                $this->getCollection()->setOrder($column, $dir);
            }

            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled())
            {
                $this->getCollection()->addStoreFilter($role->getAllowedStoreviewIds(), false);
            }

            if (!$this->_isExport)
            {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }
}
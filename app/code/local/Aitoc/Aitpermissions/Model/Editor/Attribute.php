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
class Aitoc_Aitpermissions_Model_Editor_Attribute extends Mage_Core_Model_Abstract
{
    protected $_arrayPermissions = null;

    protected function _construct()
    {
        $this->_init('aitpermissions/editor_attribute');
    }

    public function getRoleAttributeEnable($role = null)
    {
        $collection = $this->getCollection();

        return $this->_getAttributeIds($collection->getAttributeByRole($role, 1));
    }

    public function getRoleAttributeDisable($role = null)
    {
        $collection = $this->getCollection();

        return $this->_getAttributeIds($collection->getAttributeByRole($role, 0));
    }

    protected function _getAttributeIds($collection)
    {
        $array = array();
        foreach($collection as $editorArrt)
        {
            $array[] = $editorArrt->getAttributeId();
        }
        return $array;
    }

    public function getAttributePermissionByRole($role)
    {
        if($this->_arrayPermissions === null)
        {
            $collection = $this->getCollection();
            $this->_arrayPermissions = array();
            foreach($collection->getAttributeByRole($role) as $attr)
            {
                $this->_arrayPermissions[$attr->getAttributeId()] = $attr->getIsAllow();
            }
        }
        return $this->_arrayPermissions;
    }
}
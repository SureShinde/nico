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
class Aitoc_Aitpermissions_Model_Mysql4_Editor_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_post_role_id = 0;
    protected $_post_allow = 0;

    protected function _construct()
    {
        $this->_init('aitpermissions/editor_attribute');
    }

    public function getAttributeByRole($roleId = null, $if = null)
    {
        $this->getSelect()->reset(Zend_Db_Select::WHERE);
        $this->addFieldToFilter('role_id', $roleId);

        if($if !== null)
        {
            $this->addFieldToFilter('is_allow', $if);
        }
        return $this->load();
    }

    public function deleteAttributeByRole($array)
    {
        $this->_resetArrtibuteEditorFilter();
        $this->addFieldToFilter('attribute_id', array('in'=>$array));
        foreach($this->load() as $attr)
        {
            $attr->delete();
        }
    }

    public function addAttributeByRole($array)
    {
        $this->_resetArrtibuteEditorFilter();
        $data = array(
            'role_id' => $this->_post_role_id,
            'is_allow' => $this->_post_allow,
        );
        foreach($array as $attr)
        {
            $data['attribute_id'] = $attr;
            $item = Mage::getModel('aitpermissions/editor_attribute')->load(null);
            $item->setData($data);
            $item->save();
        }
    }

    protected function _resetArrtibuteEditorFilter()
    {
        $this->getSelect()->reset(Zend_Db_Select::WHERE);
        $this->addFieldToFilter('role_id', $this->_post_role_id);
        $this->addFieldToFilter('is_allow', $this->_post_allow);
    }

    public function setPostRoleId($id)
    {
        $this->_post_role_id = $id;
    }

    public function setPostAllow($allow)
    {
        $this->_post_allow = $allow;
    }

    public function duplicateAttributePermissions($old_role, $new_role)
    {
        $attributes = $this->getAttributeByRole($old_role);
        foreach($attributes as $attr)
        {
            $attr->setId(null);
            $attr->setRoleId($new_role);
            $attr->save();
        }
    }
}
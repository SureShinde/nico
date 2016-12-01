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
class Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor_Attribute extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    protected $_roleId = 0;
    public function __construct()
    {
        parent::__construct();
        $this->setId('editorAttributeGrid');
        $this->setUseAjax(true);
    }

    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return $this->getUrl('adminhtml/aitpermissions_editor/attributegrid', array('rid' => $roleId));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');

        $subquery = Mage::getSingleton('core/resource')->getConnection('core_write')->select();
        $subquery
            ->from(
                array("attr"=>Mage::getSingleton('core/resource')->getTableName("eav_entity_attribute")),
                "attr.attribute_id"
            )
            ->join(
                array("groups"=>Mage::getSingleton('core/resource')->getTableName("eav_attribute_group")),
                'attr.`attribute_group_id` = groups.`attribute_group_id`',
                array('attribute_group_name'=>new Zend_Db_Expr ('GROUP_CONCAT( DISTINCT attribute_group_name )'))
            )
            ->group('attr.attribute_id');

        $collection->getSelect()
            ->columns(
                array('is_ait_allow'=>new Zend_Db_Expr ('IF( is_global IN ( '.implode(',', $this->_getEnableScope()).' ) , 1, 0 )'))
            )
            ->joinLeft(
                array("group_name" => $subquery),
                'group_name.attribute_id = main_table.attribute_id',
                'attribute_group_name'
            );

        $collection->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        switch($column->getId()){
            case 'is_allow':
                $allowIds = $this->_getAllowAttribute();
                $this->_addInFilter($column, $allowIds, 'main_table.attribute_id');
                break;

            case 'is_disable':
                $disableIds = $this->_getDisableAttribute();
                $this->_addInFilter($column, $disableIds, 'main_table.attribute_id');
                break;

            case 'is_ait_allow':
                $scopeIds = $this->_getEnableScope();
                $this->_addInFilter($column, $scopeIds, 'is_global');
                break;

            default:
                parent::_addColumnFilterToCollection($column);

        }

        return $this;
    }

    protected function _addInFilter($column, $arrayIds, $field)
    {
        if (empty($arrayIds)) {
            $arrayIds = 0;
        }
        if ($column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter($field, array('in'=>$arrayIds));
        }
        else {
            if($arrayIds) {
                $this->getCollection()->addFieldToFilter($field, array('nin'=>$arrayIds));
            }
        }
    }
    /**
     * Prepare product attributes grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        $disablesInput = false;
        if($this->getRequest()->getParam('scope') == 'disabled')
        {
            $disablesInput = true;
        }

        $this->addColumn('is_allow', array(
            'type'      => 'radio',
            'header'    =>Mage::helper('aitpermissions')->__('Allow'),
            'name'      => 'is_allow',
            'values'    => $this->_getAllowAttribute(),
            'align'     => 'center',
            'index'     => 'attribute_id',
            'field_name'=> 'is_allow',
            'html_name'=> 'attribute_permission',
            'radio_value'=> '1',
            'disabled'=> $disablesInput,
            'renderer'  => 'Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor_Render_Radio'
        ));

        $this->addColumn('is_disable', array(
            'type'      => 'radio',
            'header'    =>Mage::helper('aitpermissions')->__('Deny'),
            'name'      => 'is_disable',
            'values'    => $this->_getDisableAttribute(),
            'align'     => 'center',
            'index'     => 'attribute_id',
            'field_name'=> 'is_disable',
            'html_name'=> 'attribute_permission',
            'radio_value'=> '0',
            'disabled'=> $disablesInput,
            'renderer'  => 'Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor_Render_Radio'
        ));

        $this->addColumn('is_ait_allow', array(
            'header'=>Mage::helper('aitpermissions')->__('Inherited from Scope'),
            'sortable'=>true,
            'index'=>'is_ait_allow',
            'name' => 'is_ait_allow',
            'field_name'=> 'is_ait_allow',
            'type' => 'options',
            'width' => '100px',
            'options' => $this->_getDisableScopeOptions(),
            'align' => 'center',
            'radio_value'=> '',
            'disabled'=> $disablesInput,
            'html_name'=> 'attribute_permission',
            'values'=> array_merge($this->_getAllowAttribute(),$this->_getDisableAttribute()),
            'renderer'  => 'Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor_Render_Radio'
            //'filter' => false,
        ));

        $this->addColumn('is_global', array(
            'header'=>Mage::helper('aitpermissions')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => $this->getScopeArray(),
            'align' => 'center',
        ));

        parent::_prepareColumns();

        $this->addColumn('attribute_group_name', array(
            'header'=>Mage::helper('aitpermissions')->__('Tabs'),
            'sortable'=>true,
            'index'=>'attribute_group_name'
        ));


        return $this;
    }

    public function getScopeArray()
    {
        return array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );
    }

    protected function _getAllowAttribute($json=false)
    {
        if ( $this->_isGetAttributeFromPost()) {
            return $this->_getValueArrayFromPost('is_allow_ids');
        }
        $attrEnable  = Mage::getModel('aitpermissions/editor_attribute')->getRoleAttributeEnable($this->_getRoleId());
        return $this->_getArrayForOption($attrEnable, $json);
    }


    protected function _getDisableAttribute($json=false)
    {
        if ( $this->_isGetAttributeFromPost()) {
            return $this->_getValueArrayFromPost('is_disable_ids');
        }
        $attrDisable  = Mage::getModel('aitpermissions/editor_attribute')->getRoleAttributeDisable($this->_getRoleId());
        return $this->_getArrayForOption($attrDisable, $json);
    }

    protected function _isGetAttributeFromPost()
    {
        if ( $this->getRequest()->getParam('is_disable_ids') != "" || $this->getRequest()->getParam('is_allow_ids') != "" || $this->getRequest()->getParam('default_ids') != "") {
            return true;
        }
        return false;
    }

    protected function _getValueArrayFromPost($name)
    {
        if($this->getRequest()->getParam($name) == "")
        {
            return array();
        }
        return $this->getRequest()->getParam($name);
    }

    protected function _getArrayForOption($array, $json)
    {
        if (sizeof($array) > 0) {
            if ( $json ) {
                $jsonAttr = Array();
                foreach($array as $attrId) $jsonAttr[$attrId] = 0;
                return Mage::helper('core')->jsonEncode((object)$jsonAttr);
            } else {
                return array_values($array);
            }
        } else {
            if ( $json ) {
                return '{}';
            } else {
                return array();
            }
        }
    }

    protected function _getDisableScopeOptions()
    {

        return array(
                1 =>Mage::helper('aitpermissions')->__('Allow'),
                0 =>Mage::helper('aitpermissions')->__('Deny'),
            );

    }

    protected function _getEnableScope()
    {
        if(!($scope = $this->getRequest()->getParam('scope')))
        {
            $scope = $this->getLayout()->getBlock('adminhtml.permissions.tab.advanced')->getScope();
            $canEditGlobal = Mage::getModel('aitpermissions/advancedrole')->canEditGlobalAttributes($this->_getRoleId());
        }
        else
        {
            $canEditGlobal = $this->getRequest()->getParam('can_edit_global');
        }

        switch($scope){
            case 'store':
                $arrayScope = array(0);
                break;
            case 'website':
                $arrayScope = array(0,2);
                break;
            default:
                $arrayScope = array(0,1,2);
        }
        if(!empty($canEditGlobal))
        {
            $arrayScope[]=1;
        }
        return $arrayScope;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    protected function _getRoleId()
    {
        if(empty($this->_roleId))
        {
            $this->_roleId = ( $this->getRequest()->getParam('rid') > 0 ) ? $this->getRequest()->getParam('rid') : Mage::registry('RID');
        }
        return $this->_roleId;
    }
}
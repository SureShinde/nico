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
class Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Create extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /* @var $form Varien_Data_Form */
        $form = new Varien_Data_Form();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('permissions_product_create', array('legend'=>Mage::helper('aitpermissions')->__('Product create permission')));
        $this->_addElementTypes($fieldset);        

        $fieldset->addField('allow_create_product', 'select', array(
            'name'     => 'allow_create_product',
            'label'    => Mage::helper('aitpermissions')->__('Allow Create Product'),
            'title'    => Mage::helper('aitpermissions')->__('Allow Create Product'),
            'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('apply_to', 'apply', array(
            'name'        => 'apply_to[]',
            'label'       => Mage::helper('catalog')->__('Allow To'),
            'values'      => Mage_Catalog_Model_Product_Type::getOptions(),
            'mode_labels' => array(
                'all'     => Mage::helper('catalog')->__('All Product Types'),
                'custom'  => Mage::helper('catalog')->__('Selected Product Types')
            ),            
        ), 'frontend_class');

        $this->setForm($form);
        $this->_setFormValues($form);
    }

    /**
     * @param Varien_Data_Form $form
     */
    protected function _setFormValues($form)
    {
        $request = Mage::app()->getRequest();
        $form->getElement('allow_create_product')->setValue('0');
        $rid = null;
        if($request->getParam('rid'))
        {
            $rid = $request->getParam('rid');
            $advancedrole = Mage::getModel('aitpermissions/advancedrole');
            if($advancedrole->canCreateProducts($rid))
            {
                $form->getElement('allow_create_product')->setValue('1');
            }

            $editorType = Mage::getModel('aitpermissions/editor_type');
            if($applyTo = $editorType->getRestrictedTypes($rid))
            {
                $applyTo = is_array($applyTo) ? $applyTo : explode(',', $applyTo);
                $form->getElement('apply_to')->setValue($applyTo);
            }
            else
            {
                $form->getElement('apply_to')->addClass('no-display ignore-validate');
            }
        }
        else
        {
            $form->getElement('apply_to')->addClass('no-display ignore-validate');
            $form->getElement('allow_create_product')->setValue('1');
        }

        $form->getElement('apply_to')->setSize(5);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("apply_to", 'apply_to')
            ->addFieldMap("allow_create_product", 'allow_create_product')
            ->addFieldDependence('apply_to', 'allow_create_product', '1')
        );
    }

    /**
     * Retrieve additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply'         => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply'),
        );
    }    
}
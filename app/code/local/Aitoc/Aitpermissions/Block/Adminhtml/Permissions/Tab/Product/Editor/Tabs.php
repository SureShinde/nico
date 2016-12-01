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
class Aitoc_Aitpermissions_Block_Adminhtml_Permissions_Tab_Product_Editor_Tabs extends Mage_Adminhtml_Block_Widget_Form
{    
    protected function _prepareForm()
    {
        /* @var $form Varien_Data_Form */
        $form = new Varien_Data_Form();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        //$fieldset = $form->addFieldset('permissions_product_editor', array('legend'=>Mage::helper('aitpermissions')->__('Disable product tabs')));
        $fieldset = $form->addFieldset('permissions_product_editor', array());
        $tabs = Mage::helper('aitpermissions')->getProductTabs();
        foreach($tabs as $name => $title)
        {
            $fieldset->addField($name, 'checkbox', array(
                'name'     => $name,
                'label'    => Mage::helper('catalog')->__($title),
                'title'    => Mage::helper('catalog')->__($title),
                'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            ));
        }

        $this->setForm($form);
        $this->_setFormValues($form);
    }

    /**
     * @param Varien_Data_Form $form
     */
    protected function _setFormValues($form)
    {
        $request = Mage::app()->getRequest();
        $rid = null;
        if($request->getParam('rid'))
        {
            $rid = $request->getParam('rid');            

            $editorTab = Mage::getModel('aitpermissions/editor_tab');
            $disabledTabs = $editorTab->getDisabledTabs($rid);
            if($disabledTabs)
            {                
                foreach($disabledTabs as $tab)
                {
                    $form->getElement($tab)->setChecked(1);
                }
            }            
        }        
    }
}
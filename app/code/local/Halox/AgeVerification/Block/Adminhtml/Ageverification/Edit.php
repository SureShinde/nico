<?php

class Halox_AgeVerification_Block_Adminhtml_Ageverification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ageverification';
        $this->_controller = 'adminhtml_ageverification';
        
        $this->_updateButton('save', 'label', Mage::helper('ageverification')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ageverification')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ageverification_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ageverification_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ageverification_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ageverification_data') && Mage::registry('ageverification_data')->getId() ) {
            return Mage::helper('ageverification')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ageverification_data')->getTitle()));
        } else {
            return Mage::helper('ageverification')->__('Add Item');
        }
    }
}
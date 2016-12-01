<?php

class Sunovisio_CustomerFlag_Block_Adminhtml_Customerflag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'customerflag';
        $this->_controller = 'adminhtml_customerflag';
        
        $this->_updateButton('save', 'label', Mage::helper('customerflag')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('customerflag')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('customerflag_data') && Mage::registry('customerflag_data')->getId() ) {
            return Mage::helper('customerflag')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('customerflag_data')->getTitle()));
        } else {
            return Mage::helper('customerflag')->__('Add Item');
        }
    }
}
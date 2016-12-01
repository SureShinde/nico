<?php

class MW_HelpDesk_Block_Adminhtml_Department_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
        $this->_controller = 'adminhtml_department';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Department'));
        $this->_updateButton('delete', 'label', Mage::helper('helpdesk')->__('Delete Department'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('department_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'department_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'department_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('department_data') && Mage::registry('department_data')->getId() ) {
            return Mage::helper('helpdesk')->__("Edit Department '%s'", $this->htmlEscape(Mage::registry('department_data')->getName()));
        } else {
            return Mage::helper('helpdesk')->__('Add Department');
        }
    }
}
<?php

class MW_HelpDesk_Block_Adminhtml_Member_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
        $this->_controller = 'adminhtml_member';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Staff'));
        $this->_updateButton('delete', 'label', Mage::helper('helpdesk')->__('Delete Staff'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('member_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'member_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'member_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('member_data') && Mage::registry('member_data')->getId() ) {
            return Mage::helper('helpdesk')->__("Edit Staff '%s'", $this->htmlEscape(Mage::registry('member_data')->getName()));
        } else {
            return Mage::helper('helpdesk')->__('Add Staff');
        }
    }
}
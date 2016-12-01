<?php

class MW_HelpDesk_Block_Adminhtml_Gateway_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
        $this->_controller = 'adminhtml_gateway';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Gateway'));
        $this->_updateButton('delete', 'label', Mage::helper('helpdesk')->__('Delete Gateway'));
		
//        $this->_addButton('saveandcontinue', array(
//            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
//            'onclick'   => 'saveAndContinueEdit()',
//            'class'     => 'save',
//        ), -100);
//
//        $this->_formScripts[] = "
//            function toggleEditor() {
//                if (tinyMCE.getInstanceById('gateway_content') == null) {
//                    tinyMCE.execCommand('mceAddControl', false, 'gateway_content');
//                } else {
//                    tinyMCE.execCommand('mceRemoveControl', false, 'gateway_content');
//                }
//            }
//
//            function saveAndContinueEdit(){
//                editForm.submit($('edit_form').action+'back/edit/');
//            }
//        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('gateway_data') && Mage::registry('gateway_data')->getId() ) {
            return Mage::helper('helpdesk')->__("Edit Gateway '%s'", $this->htmlEscape(Mage::registry('gateway_data')->getName()));
        } else {
            return Mage::helper('helpdesk')->__('Add Gateway');
        }
    }
}
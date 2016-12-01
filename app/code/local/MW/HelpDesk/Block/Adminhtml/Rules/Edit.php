<?php

class MW_HelpDesk_Block_Adminhtml_Rules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
        $this->_controller = 'adminhtml_rules';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('helpdesk')->__('Delete Rule'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_addButton('save_and_apply', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Apply'),
                'onclick'   => "$('rule_auto_apply').value=1; editForm.submit();",
                'class' => 'save'
        ), 10);
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rule_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'rule_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'rule_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('rule_data') && Mage::registry('rule_data')->getId() ) {
            return Mage::helper('helpdesk')->__("Edit Rule '%s'", $this->htmlEscape(Mage::registry('rule_data')->getName()));
        } else {
            return Mage::helper('helpdesk')->__('Add Rule');
        }
    }
}
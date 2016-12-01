<?php

class MW_HelpDesk_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
        $this->_controller = 'adminhtml_template';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Quick Response'));
        $this->_updateButton('delete', 'label', Mage::helper('helpdesk')->__('Delete Quick Response'));
		
		/*
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('template_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'template_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'template_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		*/
    }

	//add wysiwyg_config
	protected function _prepareLayout() {
	 	parent::_prepareLayout();
        if (Mage::getSingleton('helpdesk/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
       }
	}
	
    public function getHeaderText()
    {
        if( Mage::registry('template_data') && Mage::registry('template_data')->getId() ) {
            return Mage::helper('helpdesk')->__($this->htmlEscape(Mage::registry('template_data')->getTitle()));
        } else {
            return Mage::helper('helpdesk')->__('Add Quick Response');
        }
    }
}
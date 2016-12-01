<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct(); 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
         //  $this->_controller = 'hdadmin_ticket';
        $this->_controller = 'adminhtml_ticket';
        $this->_mode = 'new';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Ticket'));
        $this->updateButton('back','onclick','setLocation(\''.$this->getUrl('*/*/'.$this->getRequest()->getParam('action')).'\')');
    }

	//add wysiwyg_config
	protected function _prepareLayout() {
	 	parent::_prepareLayout();
        if (Mage::getSingleton('helpdesk/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
       }
	}
	
   	public function getHeaderCssClass() 
   	{
        return 'icon-head head-customer-groups';
    }
    
    public function getHeaderText()
    {
    	return Mage::helper('helpdesk')->__('Add Ticket');
    }
}
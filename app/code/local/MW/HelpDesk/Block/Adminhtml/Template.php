<?php
class MW_HelpDesk_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  	public function __construct()
  	{
    	$this->_controller = 'adminhtml_template';
    	$this->_blockGroup = 'helpdesk';	// module name
    	$this->_headerText = Mage::helper('helpdesk')->__('Manage Quick Response Templates');
    	$this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Quick Response');
    	parent::__construct();
  	}
}

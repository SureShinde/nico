<?php
class MW_HelpDesk_Block_Adminhtml_Gateway extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  	public function __construct()
  	{
    	$this->_controller = 'adminhtml_gateway';
    	$this->_blockGroup = 'helpdesk';	// module name
    	$this->_headerText = Mage::helper('helpdesk')->__('Manage Email Gateways');
    	$this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Gateway');
    	parent::__construct();
  	}
}

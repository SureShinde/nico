<?php
class MW_HelpDesk_Block_Adminhtml_Ticketlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ticketlog';
    $this->_blockGroup = 'helpdesk';	// module name
    $this->_headerText = Mage::helper('helpdesk')->__('Ticket Logs Information');
	//$this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Log');
	//$this->_removeButton('Add New');
    parent::__construct();
    $this->_removeButton('add');
  }
}
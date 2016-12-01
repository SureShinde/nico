<?php
class MW_HelpDesk_Block_Adminhtml_Member extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_member';
    $this->_blockGroup = 'helpdesk';	// module name
    $this->_headerText = Mage::helper('helpdesk')->__('Staff Manager');
    $this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Staff');
    parent::__construct();
  }
}
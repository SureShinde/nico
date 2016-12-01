<?php
class MW_HelpDesk_Block_Adminhtml_Rules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rules';
    $this->_blockGroup = 'helpdesk';	// module name
    $this->_headerText = Mage::helper('helpdesk')->__('Manage Rules');
    $this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Rule');
    parent::__construct();
  }
}
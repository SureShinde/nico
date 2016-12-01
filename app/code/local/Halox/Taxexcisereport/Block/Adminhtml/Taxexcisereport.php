<?php
class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_taxexcisereport';
    $this->_blockGroup = 'taxexcisereport';
    $this->_headerText = Mage::helper('taxexcisereport')->__('');
    
    parent::__construct();
	
    $this->_removeButton('add');
	
  }
}
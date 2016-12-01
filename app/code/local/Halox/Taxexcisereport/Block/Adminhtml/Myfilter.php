<?php
class Halox_Taxexcisereport_Block_Adminhtml_Myfilter extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_taxexcisereport';
    $this->_blockGroup = 'taxexcisereport';
	
    $this->_headerText = Mage::helper('taxexcisereport')->__('Excise Tax Report');
	$this->setTemplate("taxexcisereport/my_filter.phtml");
    $this->_addButtonLabel = Mage::helper('taxexcisereport')->__('Add Item');
    //parent::__construct();
	//$this->_removeButton('add');
  }
}
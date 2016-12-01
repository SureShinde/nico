<?php
class Sunovisio_CustomerFlag_Block_Adminhtml_Customerflag extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customerflag';
    $this->_blockGroup = 'customerflag';
    $this->_headerText = Mage::helper('customerflag')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('customerflag')->__('Add Item');
    parent::__construct();
  }
}
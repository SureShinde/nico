<?php
class Halox_AgeVerification_Block_Adminhtml_Ageverification extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ageverification';
    $this->_blockGroup = 'ageverification';
    $this->_headerText = Mage::helper('ageverification')->__('Age Verification Manager');
    $this->_addButtonLabel = Mage::helper('ageverification')->__('Add Item');
    parent::__construct();
  }
}
<?php

class Halox_AgeVerification_Block_Adminhtml_Ageverification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ageverification_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ageverification')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ageverification')->__('Item Information'),
          'title'     => Mage::helper('ageverification')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('ageverification/adminhtml_ageverification_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
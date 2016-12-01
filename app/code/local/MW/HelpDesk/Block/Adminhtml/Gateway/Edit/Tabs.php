<?php

class MW_HelpDesk_Block_Adminhtml_Gateway_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('gateway_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Gateway Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Gateway Information'),
          'title'     => Mage::helper('helpdesk')->__('Gateway Information'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_gateway_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
<?php

class MW_HelpDesk_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('template_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Quick Response Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Quick Response Information'),
          'title'     => Mage::helper('helpdesk')->__('Quick Response Information'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_template_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
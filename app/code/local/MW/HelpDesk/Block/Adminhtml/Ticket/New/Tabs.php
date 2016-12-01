<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_New_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ticket_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Ticket Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Ticket Information'),
          'title'     => Mage::helper('helpdesk')->__('Ticket Information'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_new_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
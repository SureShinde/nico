<?php

class MW_HelpDesk_Block_Adminhtml_Member_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('member_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Staff Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Staff Information'),
          'title'     => Mage::helper('helpdesk')->__('Staff Information'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_member_edit_tab_form')->toHtml(),
      	  'active'    => true
      ));
      
//      $this->addTab('roles_section', array(
//          'label'     => Mage::helper('adminhtml')->__('User Role'),
//          'title'     => Mage::helper('adminhtml')->__('User Role'),
//          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_member_edit_tab_form')->toHtml(),
//      ));
     
      return parent::_beforeToHtml();
  }
}
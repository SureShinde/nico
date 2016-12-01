<?php

class MW_HelpDesk_Block_Adminhtml_Department_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('department_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Department Information'));
  }

  protected function _beforeToHtml()
  {
  	  $viewTabFormBlock = $this->getLayout()->createBlock('helpdesk/adminhtml_department_edit_tab_form');
      $viewTabFormBlock->setTemplate('mw_helpdesk/widget/moderator.phtml');
      
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Department Information'),
          'title'     => Mage::helper('helpdesk')->__('Department Information'),
      	  'content'   => $viewTabFormBlock->toHtml(),
          //'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_department_edit_tab_form')->toHtml(),
      ));
      
            $this->addTab('form_assigned_operator', array(
                'label'     => Mage::helper('helpdesk')->__("Department Staff"),
                'url'       => $this->getUrl('*/*/operator', array('_current' => true)),
                'class'     => 'ajax',
            ));
     
      return parent::_beforeToHtml();
  }
}
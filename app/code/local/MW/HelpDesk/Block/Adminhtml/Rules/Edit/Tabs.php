<?php

class MW_HelpDesk_Block_Adminhtml_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('rule_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('helpdesk')->__('Rule Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('helpdesk')->__('Rule Information'),
          'title'     => Mage::helper('helpdesk')->__('Rule Information'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_form')->toHtml(),
		  'active'    =>true,
      ));
      
      $this->addTab('form_conditions', array(
          'label'     => Mage::helper('helpdesk')->__('Conditions'),
          'title'     => Mage::helper('helpdesk')->__('Conditions'),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_conditions')->toHtml(),
      	  //'active'    =>true,
      ));
      
	  $this->addTab('form_actions', array(
          'label'     => Mage::helper('helpdesk')->__('Actions'),
          'title'     => Mage::helper('helpdesk')->__('Actions'),
	  	  //'url'       => $this->getUrl('*/*/operator', array('_current' => true)),
          //'class'     => 'ajax',
          //'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_grid')->toHtml(),
          'content'   => $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_actions')->toHtml(),
      	  //'active'    =>true,
      ));
     
	  $this->addTab('form_applyto', array(
          'label'     => Mage::helper('helpdesk')->__('Rule Applied'),
          'title'     => Mage::helper('helpdesk')->__('Rule Applied'),
	  	  'url'       => $this->getUrl('*/*/operator', array('_current' => true)),
          'class'     => 'ajax',
      ));
	  
      return parent::_beforeToHtml();
  }
}
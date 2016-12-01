<?php
class MW_Helpdesk_Block_Adminhtml_Customer_Edit_Tab_Ticket extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('options_form', array(
      	  'legend'=>Mage::helper('helpdesk')->__('Ticket Information')
      ));
      
      $fieldset->addField('url', 'hidden', array(
          'name'  => 'url',
          'value' => $this->getBaseUrl()
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Set Status To'),
          'name'      => 'status',
          'values'    => Mage::getModel('helpdesk/config_source_status')->getOptionArray()
      ));

      $fieldset->addField('priority', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Priority'),
       	  'title'     => Mage::helper('helpdesk')->__('Priority'),
          'name'      => 'priority',
      	  'value'     => 2,
		  'values'    => Mage::getModel('helpdesk/config_source_priority')->getOptionArray()
      ));
      
      $departments = array();
      $departments[''] = '-- Please select Department --';
	  $collection = Mage::getModel('helpdesk/department')->getCollection()
	  					->addFieldToFilter('active', array('eq' => 1));;
	  foreach ($collection as $department) {
		 	$departments[$department->getId()] = $department->getName();
	  }
      $fieldset->addField('department_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Assign To Department'),
          'name'      => 'department_id',
      	  'class'     => 'required-entry',
          'required'  => true,
          'values'    => $departments
      ));
      
      $fieldset->addField('moderator', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Staff'),
          'name'      => 'moderator',
      ));
      
      $fieldset->addField('sender', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Assign To Client'),
      	  'title' => Mage::helper('helpdesk')->__('Assign To Client'),
          'class' => 'required-entry validate-email',
          'required'  => true,
          'name'      => 'sender',
      ));

      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Name'),
          'name'      => 'name',
      ));

      $fieldset->addField('order', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Assign to Order'),
          'name'      => 'order',
      ));
      
      $fieldset->addField('tags', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Tags'),
          'name'      => 'tags',
      ));
      
      $fieldset->addField('subject', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Subject'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'subject',
      	  //'note'      => Mage::helper('helpdesk')->__('Comment'),
      ));
      
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('helpdesk')->__('Content'),
          'title'     => Mage::helper('helpdesk')->__('Content'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
      
      $fieldset->addField('file_attachment', 'file', array(
          'label'     => Mage::helper('helpdesk')->__('Attach File'),
//          'required'  => false,
          'name'      => 'file_attachment',
	  ));
      return parent::_prepareForm();
  }
  
  
/**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('helpdesk')->__('Add Ticket');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('helpdesk')->__('Add Ticket');
    }

    /**
     * Can show tab flag
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check is a hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}



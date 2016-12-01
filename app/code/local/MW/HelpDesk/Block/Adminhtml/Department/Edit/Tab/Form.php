<?php

class MW_HelpDesk_Block_Adminhtml_Department_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('helpdesk_form', array('legend'=>Mage::helper('helpdesk')->__('Detail Information')));
      
      $fieldset->addField('url', 'hidden', array(
          'name'  => 'url',
          'value' => $this->getBaseUrl()
      ));
      
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Department Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
      
      $fieldset->addField('dcode', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Dept. Code'),
          'name'      => 'dcode',
      ));
      
      $fieldset->addField('active', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Active'),
          'name'      => 'active',
          'options'   => array(
          	'1' => Mage::helper('helpdesk')->__('Yes'),
            '2' => Mage::helper('helpdesk')->__('No'))
      ));
      
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('stores', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'class'     => 'required-entry',
          		'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
//        else {
//            $fieldset->addField('store_id', 'hidden', array(
//                'name'      => 'stores[]',
//                'value'     => Mage::app()->getStore(true)->getId()
//            ));
//            $model->setStoreId(Mage::app()->getStore(true)->getId());
//        }
      
      $fieldset->addField('required_login', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Required Login'),
          'name'      => 'required_login',
          'options'   => array(
          	'1' => Mage::helper('helpdesk')->__('Yes'),
            '2' => Mage::helper('helpdesk')->__('No'))
      ));
      
      $gateways = array();
      $gateways[''] = '-- Please select Gataway --';
	  $collection = Mage::getModel('helpdesk/gateway')->getCollection()
	  				->addFieldToFilter('active', array('eq' => 1));
	  foreach ($collection as $gateway) {
		 	$gateways[$gateway->getId()] = $gateway->getName();
	  }
	  $fieldset->addField('default_gateway', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Please Select Gateway'),
          'name'      => 'default_gateway',
          'values'    => $gateways,
      ));
      
      
      $fieldset->addField('moderator', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Department Moderator'),
          'name'      => 'moderator',
      	  'note'	  => 'Enter Moderator Email address',
      ));
      
      $fieldset->addField('auto_notification', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Auto-Notification'),
          'name'      => 'auto_notification',
          'options'   => array(
          	'1' => Mage::helper('helpdesk')->__('Yes'),
            '2' => Mage::helper('helpdesk')->__('No'))
      ));
      
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('helpdesk')->__('Description'),
          'title'     => Mage::helper('helpdesk')->__('Description'),
          'style'     => 'width:500px; height:150px;',
          'wysiwyg'   => false
      ));
      
      $fieldset->addField('department_sort_order', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Dept. Sort Order'),
          'name'      => 'department_sort_order',
      ));
      
      $fieldset2 = $form->addFieldset('helpdesk_form2', array('legend'=>Mage::helper('helpdesk')->__('Department Email Templates (Override General Email Templates)')));
      
	  $emailTemplate = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
	 
	  $fieldset2->addField('status_new_ticket_customer', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Client New Ticket Notification'),
          'name'      => 'status_new_ticket_customer',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
      
      $fieldset2->addField('new_ticket_customer', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Client New Ticket Template (Notify customer of new ticket)'),
          'name'      => 'new_ticket_customer',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_reply_ticket_customer', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Staff Response Notification'),
          'name'      => 'status_reply_ticket_customer',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('reply_ticket_customer', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Staff Response Template (Notify client of the reply from staff)'),
          'name'      => 'reply_ticket_customer',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_new_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Staff New Ticket Notification'),
          'name'      => 'status_new_ticket_operator',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('new_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Staff New Ticket Template (Notify staff of new ticket)'),
          'name'      => 'new_ticket_operator',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_reply_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Client Response Notification'),
          'name'      => 'status_reply_ticket_operator',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('reply_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Client Response Template (Notify staff of the reply from client)'),
          'name'      => 'reply_ticket_operator',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_reassign_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Ticket Reassign Notification'),
          'name'      => 'status_reassign_ticket_operator',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('reassign_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Ticket Reassignment Template (Notify staff of ticket reassignment)'),
          'name'      => 'reassign_ticket_operator',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_late_reply_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Late Response Notification'),
          'name'      => 'status_late_reply_ticket_operator',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('late_reply_ticket_operator', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Late Response Template (Notify staff of a missed ticket)'),
          'name'      => 'late_reply_ticket_operator',
          'values'   => $emailTemplate->toOptionArray()
      ));
	  
	  $fieldset2->addField('status_internal_note_notification', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Enable Internal Note Notification'),
          'name'      => 'status_internal_note_notification',
          'values'   => Mage::getModel('helpdesk/config_source_status')->toOptionEmailNotify()
      ));
	  
      $fieldset2->addField('internal_note_notification', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Internal Note Notification Template (Notify Staff by Email)'),
          'name'      => 'internal_note_notification',
          'values'   => $emailTemplate->toOptionArray()
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getDepartmentData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDepartmentData());
          Mage::getSingleton('adminhtml/session')->setDepartmentData(null);
      } elseif ( Mage::registry('department_data') ) {

          $form->setValues(Mage::registry('department_data')->getData());
          $form->getElement('url')->setValue($this->getBaseUrl());
          
          // set moderator
      	  if($this->getRequest()->getParam('id')) {
        	$members = Mage::getResourceModel('helpdesk/member_collection')
        		->addFieldToFilter('member_id', array('eq' => Mage::registry('department_data')->getMemberId()));
		   	foreach ($members as $member) {
		   		$form->getElement('moderator')->setValue($member->getEmail());
   			}
	  	  }
      }
      return parent::_prepareForm();
  }
}
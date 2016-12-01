<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_New_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $wysiwygConfig = Mage::getSingleton('helpdesk/wysiwyg_config')->getConfig();
	  
      $fieldset = $form->addFieldset('options_form', array(
      	  'legend'=>Mage::helper('helpdesk')->__('Ticket Information')
      ));
      
	  $helper = Mage::helper('helpdesk/data');
	  $base_url = $helper->_getBaseUrl(0);
	  //'value' => $this->getBaseUrl()
	  
      $fieldset->addField('url', 'hidden', array(
          'name'  => 'url',
          'value' => $base_url
      ));
      $fieldset->addField('clienturl', 'hidden', array(
          'name'  => 'clienturl',
          'value' => Mage::helper('adminhtml')->getUrl('helpdesk/autocompletefix/client')
      ));
      $fieldset->addField('orderurl', 'hidden', array(
          'name'  => 'orderurl',
          'value' => Mage::helper('adminhtml')->getUrl('helpdesk/autocompletefix/order')
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
      
      $fieldset->addField('id_category', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Select Response Folder'),
       	  'title'     => Mage::helper('helpdesk')->__('Select Response Folder'),
          'name'      => 'id_category',
		  'values'    => Mage::getModel('helpdesk/config_source_priority')->getOptionArrCateResponse()
      ));
      
      $templates = array();
      $templates[''] = '-- Please Select Template --';
      $collection = Mage::getModel('helpdesk/template')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  

	foreach ($collection as $template) {
		 	$templates[$template->getTemplateId()] = $template->getTitle();
	  }
      $fieldset->addField('template_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Use Response Template'),
          'name'      => 'template_id',
          'values'    => $templates
      ));
      
  	 try {
            $config = Mage::getSingleton('helpdesk/wysiwyg_config')->getConfig();  
            $config->setData(Mage::helper('helpdesk')->recursiveReplace(
                            '/hdadmin/', '/' . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/', $config->getData()
                    )
            );
        } catch (Exception $ex) {
            $config = null;
      }
      
      $fieldset->addField('_content', 'editor', array(
          'name'      => '_content',
          'label'     => Mage::helper('helpdesk')->__('Content'),
          'title'     => Mage::helper('helpdesk')->__('Content'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => true,
		  'config'    => $config,
          'required'  => false,
      ));
      
      $fieldset->addField('file_attachment', 'file', array(
          'label'     => Mage::helper('helpdesk')->__('Attach File'),
//          'required'  => false,
          'name'      => 'file_attachment',
	  ));

      if ($this->getRequest()->getParam('customer_id')) {
          $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('customer_id'));
          $form->getElement('sender')->setValue($customer->getEmail());
          $form->getElement('name')->setValue($customer->getFirstname() . ' ' . $customer->getLastname());
      }
      
      if ($this->getRequest()->getParam('oid')) {
          $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('oid'));
          $form->getElement('sender')->setValue($order->getCustomerEmail());
          $form->getElement('name')->setValue($order->getCustomerFirstname() . ' ' . $order->getCustomerLastname());
          $form->getElement('order')->setValue($order->getIncrementId());
          //
      }
	  
      return parent::_prepareForm();
  }
  
}
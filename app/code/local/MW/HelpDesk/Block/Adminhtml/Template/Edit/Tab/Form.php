<?php

class MW_HelpDesk_Block_Adminhtml_Template_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('helpdesk_form', array('legend'=>Mage::helper('helpdesk')->__('Quick Response Information')));
      $wysiwygConfig = Mage::getSingleton('helpdesk/wysiwyg_config')->getConfig();
           
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Quick Response Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
      $fieldset->addField('id_category', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Select Response Folder'),
       	  'title'     => Mage::helper('helpdesk')->__('Select Response Folder'),
          'name'      => 'id_category',
      	  'class'     => 'required-entry',
      	  'note'	  => 'Create response folder from helpdesk configuration',
      	  'required'  => true,
		  'values'    => Mage::getModel('helpdesk/config_source_priority')->getOptionArrCateResponse()
      ));
      
      $fieldset->addField('active', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Active'),
          'name'      => 'active',
          'values'    => array(
          	  array(
                  'value'     => 1,
                  'label'     => Mage::helper('helpdesk')->__('Yes'),
              ),
          	  array(
                  'value'     => 2,
                  'label'     => Mage::helper('helpdesk')->__('No'),
              ),
          ),
      ));
      
  	  try {
            $config = Mage::getSingleton('helpdesk/wysiwyg_config')->getConfig();     
            $config->setData(Mage::helper('helpdesk')->recursiveReplace(
                            '/hdadmin/', '/' . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/', $config->getData()));
      } catch (Exception $ex) {
            $config = null;
      }
      
	  $fieldset->addField('message', 'editor', array(
          'name'      => 'message',
          'label'     => Mage::helper('helpdesk')->__('Quick Response Message'),
          'title'     => Mage::helper('helpdesk')->__('Quick Response Message'),
          'style'     => 'width:700px; height:200px;',
	      'wysiwyg'   => true,
		  'config'    => $config,
          'required'  => true,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getTemplateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTemplateData());
          Mage::getSingleton('adminhtml/session')->setTemplateData(null);
      } elseif ( Mage::registry('template_data') ) {
          $form->setValues(Mage::registry('template_data')->getData());
      }
      return parent::_prepareForm();
  }
}
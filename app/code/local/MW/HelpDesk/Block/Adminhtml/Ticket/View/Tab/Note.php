<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tab_Note extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('statific_form', array(
      	  'legend'=>Mage::helper('helpdesk')->__('Internal Note Information')
      ));
      
      $fieldset->addField('quicknote', 'text', array(
          'label' => Mage::helper('helpdesk')->__('Quick Note'),	
          'name'      => 'quicknote',
      	  'style'     => 'width:400px;',
	      ));
      
      $fieldset->addField('note', 'editor', array(
          'name'      => 'note',
          'label'     => Mage::helper('helpdesk')->__('Details'),
          'title'     => Mage::helper('helpdesk')->__('Details'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
      ));
    
	 $fieldset->addField('is_sent', 'checkbox', array(
      	'name'      => 'is_sent',
        'label'     => Mage::helper('helpdesk')->__('Notify Staff by Email'),
        'value'     => 1,
   	  ));
   	  
      if ( Mage::getSingleton('adminhtml/session')->getTicketData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTicketData());
          Mage::getSingleton('adminhtml/session')->setTicketData(null);
      } elseif ( Mage::registry('ticket_data') ) {
          $form->setValues(Mage::registry('ticket_data')->getData());
          //$form->getElement('url')->setValue($this->getBaseUrl());
      }
      
      return parent::_prepareForm();
  }
}
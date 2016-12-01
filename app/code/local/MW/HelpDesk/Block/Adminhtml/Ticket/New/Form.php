<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_New_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  { 
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'),'action'=>$this->getRequest()->getParam('action'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}
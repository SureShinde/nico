<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $helper = Mage::helper('helpdesk/data');
	  //echo 'abc: ' . Mage::registry('ticket_data')->getStoreView();
      $fieldset = $form->addFieldset('statific_form', array(
      	  'legend'=>Mage::helper('helpdesk')->__('Thread Links')
      ));
      
	  /*
	  if(Mage::registry('ticket_data')->getStoreView() != '0')
      {
	  */
			//$base_url = Mage::getUrl('', array('_store' => Mage::registry('ticket_data')->getStoreView()));
			$base_url = $helper->_getBaseUrl(Mage::registry('ticket_data')->getStoreView());
			$fieldset->addField('sender', 'note', array(
	          'label' => Mage::helper('index')->__('Client Direct Link'),
	          'title' => Mage::helper('index')->__('Client Direct Link'),	
	          'text'  => '<a target = "_blank" href ="' .$base_url . 'helpdesk/viewticket/customer/code/' . Mage::registry('ticket_data')->getData('code_customer') . '">'
	          . $base_url . 'helpdesk/viewticket/customer/code/' . Mage::registry('ticket_data')->getData('code_customer') .
	        '</a>',   
	
	          'note'   => 'Client can view detail ticket thread and quick reply by click on this link',
	      	));
			
			$fieldset->addField('created_time', 'note', array(
				  'label' => Mage::helper('index')->__('Staff Direct Link'),
				  'title' => Mage::helper('index')->__('Staff Direct Link'),	
				  'text'  => '<a target = "_blank" href ="' . $base_url . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') . '">'
				  . $base_url . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') .
				'</a>',

				  'note'   => 'Staff can view detail ticket thread and quick reply by click on this link',
			));
	  /*
	  }
	  else{
		  $fieldset->addField('sender', 'note', array(
			  'label' => Mage::helper('index')->__('Client Direct Link'),
			  'title' => Mage::helper('index')->__('Client Direct Link'),	
			  'text'  => '<a target = "_blank" href ="' . $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . Mage::registry('ticket_data')->getData('code_customer') . '">'
			  . $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . Mage::registry('ticket_data')->getData('code_customer') .
			'</a>',   

			  'note'   => 'Client can view detail ticket thread and quick reply by click on this link',
		  ));
		  
		  $fieldset->addField('created_time', 'note', array(
          'label' => Mage::helper('index')->__('Staff Direct Link'),
          'title' => Mage::helper('index')->__('Staff Direct Link'),	
          'text'  => '<a target = "_blank" href ="' . $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') . '">'
          		 	 . $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') .
          		 	 '</a>',

          'note'   => 'Staff can view detail ticket thread and quick reply by click on this link',
		));
      }
	  */
	  
	  /*
      $fieldset->addField('created_time', 'note', array(
          'label' => Mage::helper('index')->__('Staff Direct Link'),
          'title' => Mage::helper('index')->__('Staff Direct Link'),	
          'text'  => '<a target = "_blank" href ="' . Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') . '">'
          		 	 . Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . Mage::registry('ticket_data')->getData('code_member') .
          		 	 '</a>',

          'note'   => 'Staff can view detail ticket thread and quick reply by click on this link',
      ));
	  */
	  $item_id = Mage::registry('ticket_data')->getItemId();
      
      if($item_id != ''){
      		$fieldset->addField('item_direct_link', 'note', array(
	          'label' => Mage::helper('index')->__('Item Direct Link'),
	          'title' => Mage::helper('index')->__('Item Direct Link '),	
	          'text'  => '<a target = "_blank" href ="' . Mage::getBaseUrl() . 'itm/' . $item_id . '?item=' . $item_id . '">'
          		 	 . Mage::getBaseUrl() . 'itm/' . $item_id . '?item=' . $item_id .
          		 	 '</a>',

          	  //'note'   => 'Staff can view detail ticket thread and quick reply by click on this link',
     		));
      }
	  
     /*
      $fieldset->addField('note', 'editor', array(
          'name'      => 'note',
          'label'     => Mage::helper('helpdesk')->__('Note'),
          'title'     => Mage::helper('helpdesk')->__('Note'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
      ));
      
      $fieldset->addField('is_sent', 'checkbox', array(
      	'name'      => 'is_sent',
        'label'     => Mage::helper('helpdesk')->__('Notify Staff by Email'),
        'value'     => 1,
   	  ));
   	  */
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
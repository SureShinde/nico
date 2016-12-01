<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('statific_form', array(
      	  'legend'=>Mage::helper('helpdesk')->__('Client Information')
      ));
      //$wysiwygConfig = Mage::getSingleton('helpdesk/wysiwyg_config')->getConfig();
       
      $fieldset->addField('subject', 'text', array(
          'label' => Mage::helper('helpdesk')->__('Subject'),	
          'name'      => 'subject',
      ));
      
      $fieldset->addField('sender', 'text', array(
          'label' => Mage::helper('helpdesk')->__('Customer Email Address'),	
          'name'      => 'sender',
      ));
      
	 // Zend_debug::dump(Mage::registry('ticket_data')->getData('created_time'));die();
      $fieldset->addField('created_time', 'note', array(
          'label' => Mage::helper('index')->__('Time Created'),
          'title' => Mage::helper('index')->__('Time Created'),	
          'text'  => '<strong>'
      			   . Mage::helper('helpdesk')->locale_time_format(Mage::getModel('core/date')->timestamp(Mage::registry('ticket_data')->getData('created_time')),Mage_Core_Model_Locale::FORMAT_TYPE_FULL,"H:i:s") //date('F jS, Y, h:i A', Mage::getModel('core/date')->timestamp(Mage::registry('ticket_data')->getData('created_time')))
      			   . '</strong>'
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
		  'values'    => Mage::getModel('helpdesk/config_source_priority')->getOptionArray()
      ));
      
      $departments = array();
      $departments[0] = '-- Please select department --';
	  $collection = Mage::getModel('helpdesk/department')->getCollection()
	  				->addFieldToFilter('active', array('eq' => 1));
	  foreach ($collection as $department) {
		 	$departments[$department->getId()] = $department->getName();
	  }
      $fieldset->addField('department_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Assign To Department'),
          'name'      => 'department_id',
          'values'    => $departments,
      	  'note'	=> 'This ticket will be sent to Moderator of chosen department',
      ));
      
      $fieldset->addField('url', 'hidden', array(
          'name'  => 'url',
          'value' => $this->getBaseUrl(),
      ));
      
	  $mw_email = null;
      $member = Mage::getModel('helpdesk/member')->load(Mage::registry('ticket_data')->getData('member_id'));
      if($member->getEmail()=='') $mw_email = 'Not Assigned'; else $mw_email = $member->getEmail();
	  
      $member = Mage::getModel('helpdesk/member')->load(Mage::registry('ticket_data')->getData('member_id'));
      $fieldset->addField('current_operator', 'note', array(
          'label' => Mage::helper('index')->__('Assigned Staff'),
          'title' => Mage::helper('index')->__('Assigned Staff'),	
          'text'  => '<strong>' . $member->getName() . ' &lt;' . $mw_email .'&gt;</strong>'
      ));
      
	  $fieldset->addField('is_assign_staff', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('Remove Staff'),
      		//'checked'   => $chk1,
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_assign_staff',
	  ));
	  
      $fieldset->addField('operator', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Assign to New Staff'),
      	  'note'	=> 'Staff Email',
          'name'      => 'operator',
      ));
      
      if(Mage::registry('ticket_data')->getData('department_id') == 0
      		&& Mage::registry('ticket_data')->getData('member_id') == 0 ) {
      
	      $fieldset = $form->addFieldset('message_form', array(
	      	  'legend'    => Mage::helper('helpdesk')->__('Ticket Message')
	      ));

	      $fieldset->addField('addcontent', 'editor', array(
	          'name'      => 'addcontent',
	          'label'     => Mage::helper('helpdesk')->__('Content'),
	          'title'     => Mage::helper('helpdesk')->__('Content'),
	          'style'     => 'width:700px; height:200px;',
	          //'wysiwyg'   => true,
		  	  //'config'    => $wysiwygConfig,
	          'required'  => false,
	      ));
      }
      
      $fieldset->addField('tags', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Tags'),
          'name'      => 'tags',
      ));
      
      //if(Mage::registry('ticket_data')->getData('order')) {
      	$order_id = Mage::registry('ticket_data')->getData('order');
      	$order = Mage::getModel('sales/order')->load($order_id);
      	$increment_id = $order->getIncrementId();
      	
      	$mw_order = null;
      	if($order_id == '') 
      		$mw_order = '<strong>' . ' &lt;' .'Not Assigned' .'&gt;</strong>'; 
      	else $mw_order = '<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id)) . '" onclick="this.target=\'blank\'">#'  
      					 . $increment_id . '</a>' . " (\${$order->getGrandTotal()}) " . ' on ' . Mage::helper('helpdesk')->locale_time_format(Mage::getModel('core/date')->timestamp($order->getCreatedAt()),Mage_Core_Model_Locale::FORMAT_TYPE_FULL); //date('F jS, Y', Mage::getModel('core/date')->timestamp($order->getCreatedAt()));
      	
      	$fieldset->addField('order', 'note', array(
          	'label'     => Mage::helper('helpdesk')->__('Order #'),
          	'text'      => $mw_order
      	));

		$fieldset->addField('is_assign_order', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('Remove Order'),
      		//'checked'   => $chk1,
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_assign_order',
		));
		
      	  $id = $this->getRequest()->getParam('id');
          $model = Mage::getModel('helpdesk/ticket')->load($id);
          $email = $model->getSender();
          
	      $departments = array();
	      $departments[0] = 'Other Order ID';
		  $collection = Mage::getModel('helpdesk/ticket')->getCollection()
		  				->addFieldToFilter('sender', array('eq' => $email));
	   	  //$collection->getSelect()->where('sender = "'. $email .'"');

		  foreach ($collection as $department) {
//		  		$_num_zero = ''; $_len_zero = strlen(trim($department->getOrder()));
//		  		for($i=0; $i<8-$_len_zero; $i++)$_num_zero .= "0";
//		  		$_num_zero_1 ="1" . $_num_zero . $department->getOrder();
				$order = Mage::getModel('sales/order')->load($department->getOrder());
				if($order->getIncrementId() != null){
			 		$departments[$department->getOrder()] = $order->getIncrementId() . 
			 									" (\${$order->getGrandTotal()}) " . ' on ' . 
			 									Mage::helper('helpdesk')->locale_time_format(Mage::getModel('core/date')->timestamp($order->getCreatedAt()),Mage_Core_Model_Locale::FORMAT_TYPE_FULL);
			 									//date('F jS, Y', Mage::getModel('core/date')->timestamp($order->getCreatedAt()));
				}
		  }
		  //Zend_debug::dump($departments);die;
	      $fieldset->addField('update_order', 'select', array(
	          'label'     => Mage::helper('helpdesk')->__('Assign to Order #'),
	          'name'      => 'update_order',
	          'values'    => $departments,
	      ));
      //}
      
      $fieldset->addField('update_order1', 'text', array(
          //'label'     => Mage::helper('helpdesk')->__('Assign to Order #'),
          'name'      => 'update_order1',
		  'note'	=> 'This is Order #. Ex: 100000001, 100000002, ...',
      	));
      
      $fieldset->addField('time_to_process', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Estimated Time'),
          'name'      => 'time_to_process',
          'note'    => Mage::helper('helpdesk')->__('By Hour'),
          'class'   => 'validate_number',
      ));
      
      /*	comment line	*/
      /*
	  $fieldset->addField('item_id', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Item ID'),
          'name'      => 'item_id',
          'note'    => Mage::helper('helpdesk')->__('Item id from ebay email'),
      ));
      
      $fieldset->addField('buyer_username', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Buyer Username'),
          'name'      => 'buyer_username',
          'note'    => Mage::helper('helpdesk')->__('Buyer username from ebay email'),
      ));
      
      $fieldset->addField('email_ref_id', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Email reference ID'),
          'name'      => 'email_ref_id',
          'note'    => Mage::helper('helpdesk')->__('Email reference ID from ebay email'),
      ));
	  
	 */
      if(Mage::registry('ticket_data')->getData('file_attachment')) {
      	$file_attachments = Mage::registry('ticket_data')->getData('file_attachment');
      	$file_attachments = explode(";",$file_attachments);
      	$i = 0;
      	foreach ($file_attachments as $file_attachment) {
      		if ($file_attachment != null) {
	      		$fieldset->addField('file_attachment'.$i++, 'note', array(
		          	'label'     => Mage::helper('helpdesk')->__('File Attachment(s)'),
		          	'text'      => '<a href="' . Mage::getBaseUrl('media') .'ticket' . DS. $file_attachment . '" onclick="this.target=\'blank\'">'  . $file_attachment . '</a>'
		      	));
      		}
      	}
      }
      //$this->getUrl('*/*/download', array('file' => rawurlencode($file_attachment)))
      if ( Mage::getSingleton('adminhtml/session')->getTicketData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTicketData());
          Mage::getSingleton('adminhtml/session')->setTicketData(null);
      } elseif ( Mage::registry('ticket_data') ) {
		  // *****  
          $form->setValues(Mage::registry('ticket_data')->getData());
          $form->getElement('url')->setValue($this->getBaseUrl());
          
          // load tags
          $tags = '';
          $collection = Mage::getModel('helpdesk/tag')->getCollection()
			->addFieldToFilter('ticket_id', array('eq' => Mage::registry('ticket_data')->getTicketId()));
      	  foreach ($collection as $tag) {
			$tags .= $tag->getName() . ', ';
		  }
          $form->getElement('tags')->setValue(trim($tags, ', '));
      }
      return parent::_prepareForm();
  }
}
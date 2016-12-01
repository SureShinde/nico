<?php

class MW_HelpDesk_Model_History extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/history');
    }
    
    /**
     * @param $data string content email
     * @return bool
     */
    private function _closeTicketViaEmail($data) 
    {
		$findme   = '[close]';
		$pos = strpos($data, $findme);
		if ($pos !== false) {
			return true;
		} else {
			return false;
		}
    }
    
    /**
     * @param $data string content email
     * @return bool
     */
    private function _reassignTicketViaEmail($data) 
    {
		if (preg_match('/reassign::{1}\w+@\w+.\w+/', $data, $matches)) {
			return substr($matches[0],10);
		} else {
			return null;
		}
    }
    
	public function saveHistoryFromGateway($data)
    {
    	// tim ticket
    	$ticketId = '';
		$storeId = Mage::app()->getStore()->getId();
		$helper = Mage::helper('helpdesk/data');
		/*** update code: get code id from $data['sub'] */
    	//$sub = explode(" ", $data['sub']);
		$sub = $data['sub'];
    	//Mage::log('Sub: ' . $sub) ;
    	$tickets = Mage::getModel('helpdesk/ticket')->getCollection()
			    		->addFieldToFilter('code_id', array('like' => "%$sub%"));
    	if(sizeof($tickets)>0) {
    		foreach ($tickets as $ticket) {
    			$ticketId = $ticket->getId();
    		}
    	}

    	// load ticket & save
		$ticket = Mage::getModel('helpdesk/ticket')->load($ticketId);
		
		// operator response with statement [close] or [reassign::operatorName]
		$memberCloseOrReassign = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
		if($memberCloseOrReassign->getEmail() == $data['sender']) {
			$flagClose = $this->_closeTicketViaEmail($data['content']);
			if($flagClose) {
				$ticket->setReplyBy(1)	// member
					   ->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED)
					   ->setLastReplyTime(now())
					   ->save();
				return;
			}
			$emailReassign = $this->_reassignTicketViaEmail($data['content']);
			if($emailReassign) {
				$members = Mage::getModel('helpdesk/member')->getCollection()
			    		->addFieldToFilter('email', array('eq' => $emailReassign));
	    		foreach($members as $member) {
	    			$lastTicketId = Mage::getModel('helpdesk/ticket')->_getLastTicketId();
					$codeMember = md5($member->getId(). $lastTicketId);
	    			$ticket->setMemberId($member->getId())
				   			->setCodeMember($codeMember)
				   			->save();
	    		}
	    		Mage::getModel('helpdesk/ticket')->reassignTicket($ticketId);
				return;
			}
		}
		
		
		$ticket->setLastReplyTime(now());
		// customer gui tra loi cho member
		$cols_member = Mage::getModel('helpdesk/member')->getCollection()->addFieldToFilter('email', $data['sender']);
		$gateways = Mage::getModel('helpdesk/gateway')->getCollection()->addFieldToFilter('email', $data['sender']);
		
		if($ticketId != '' && sizeof($cols_member) == 0 && sizeof($gateways) == 0) {
			$ticket ->setReplyBy(1)	// customer
		   			->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN)
         		   	->save();
		}	
		else {  // member tra loi
   	   	   	$ticket->setReplyBy(2)	// member
				   ->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING);
		   	$ticket->save();
		}	
		
		/* -------------------- check condition rule -------------------- */
		$model_ticket_rules = Mage::getModel('helpdesk/rules');
		//when ticket updated from fronend
		$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('updated',$ticket->getId(), 1);
		/* ------------------------------------------------------------- */
		
		// save history
		$historyData['ticket_id'] = $ticketId;
		$historyData['member_id'] = $ticket->getData('member_id');
		$historyData['department_id'] = $ticket->getData('department_id');
		$historyData['sender'] = $data['sender'];

		$historyData['content'] = $data['content'];
		$historyData['file_attachment'] = $data['file_attachment'];
		$this->setData($historyData)
			  ->setCreatedTime(now());			 
		//--------------------
   		// sent mail
		
		  if(sizeof($cols_member) > 0) { // member tra loi cho customer
		  	$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			$to = $ticket->getSender();	// gui den customer
			
			$mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
			$mailData['message'] = $data['content'];
			
			$mailData['operator_name'] = $member->getName();
			$this->setName($mailData['operator_name']);
			$mailData['customer_name'] = $ticket->getName();//here
			$mailData['expired_time'] = Mage::getStoreConfig('helpdesk/config/complete');
			
			//$mailData['customer_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			
			$base_url = $helper->_getBaseUrl($ticket->getStoreView());
			$mailData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
				
			/*
			if($ticket->getStoreView() != '0')
			{
				$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
				$mailData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			}
			else{
				$mailData['customer_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			}
			*/
			
		    if($data['file_attachment'] != '') {
				$mailData['file_attachment'] = $data['file_attachment'];
			}
			$department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
		  	$mailData['department_name'] = $department->getName();
			$templateId = $department->getReplyTicketCustomer();
		    if($templateId == '') {
	   			$storeId = Mage::app()->getStore()->getId();  
	   			$template = 'helpdesk/helpdesk_email_temp/reply_ticket_customer';
				$templateId = Mage::getStoreConfig($template,$storeId); 
	   		}
			
		    if($department->getDefaultGateway() == '') {
   				return 'Error config. Not exits DefaultGateway. Please contact admin.';
   			}
	   		$gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
	   		$from = $gateway->getEmail();
	   		
	   		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reply_ticket_customer',$storeId) == 1)
			{	   		
				Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
			}
			
			/* save ticket log */
			Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket->getId(), Mage::helper('helpdesk')->__('Staff Response'));
			/*	end save log	*/
		  } else {	//customer tra loi member
		  	$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			$to = $member->getEmail(); // gui den member
			$mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
			$mailData['message'] = $data['content'];
			$mailData['operator_name'] = $member->getName();
			$mailData['customer_name'] = $ticket->getName();//here
			$this->setName($mailData['customer_name']);
			
			//$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			
			$base_url = $helper->_getBaseUrl($ticket->getStoreView());
			$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			
			/*
			if($ticket->getStoreView() != '0')
			{
				$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
				$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			}
			else{
				$mailData['operator_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			}
			*/
			
		    if($data['file_attachment'] != '') {
				$mailData['file_attachment'] = $data['file_attachment'];
			}
		  	if($ticket->getNote() != '') {
				$mailData['note'] = $ticket->getNote();
			}
			$department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
		  	$mailData['department_name'] = $department->getName();
			$templateId = $department->getReplyTicketOperator();
		    if($templateId == '') {
	   			$storeId = Mage::app()->getStore()->getId();  
	   			$template = 'helpdesk/helpdesk_email_temp/reply_ticket_operator';
				$templateId = Mage::getStoreConfig($template,$storeId); 
	   		}
			
		  	if($department->getDefaultGateway() == '') {
   				return 'Error config. Not exits DefaultGateway. Please contact admin.';
   			}
	   		$gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
	   		$from = $gateway->getEmail();
			
	   		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reply_ticket_operator',$storeId) == 1)
			{
				Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
			}
			/* save ticket log */
			Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket->getId(), Mage::helper('helpdesk')->__('Customer Response'));
			/*	end save log	*/
		  }
		  $this->save();
    }
      
    /*
     * save history for moderator reply & member reply
     * 
		array(5) {
		  ["ticket_id"]=>
		  string(2) "74"
		  ["memberCode"]=>
		  string(32) "c4ca4238a0b923820dcc509a6f75849b"
		  ["template"]=>
		  string(0) ""
		  ["content"]=>
		  string(6) "asdasd"
		  ["close"]=>
		  string(14) "close"
		}
     */
	public function saveHistory($data)
    {
    	//echo'<pre>';var_dump($data);die();
    	$helper = Mage::helper('helpdesk/data');
    	$ticket = Mage::getModel('helpdesk/ticket')->load($data['ticket_id']);
		$ticket->setLastReplyTime(now());
			// member gui tra loi
			if(isset($data['memberCode'])) {
				$ticket->setReplyBy(2);	// member
			    if($data['no_change_status'] == 2) {
					$ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING);
				}	   
			} else {
				$ticket->setReplyBy(1)	// customer
					   ->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN);
			}	
		$ticket->save();
		
		if ($data['content'] == "" && $data['file_attachment'] == "") return;
		
		/* -------------------- check condition rule -------------------- */
		$model_ticket_rules = Mage::getModel('helpdesk/rules');
		//when ticket updated from fronend
		$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('updated', $data['ticket_id'], 2);
		/* ------------------------------------------------------------- */
		
		$data['member_id'] = $ticket->getData('member_id');
		$data['department_id'] = $ticket->getData('department_id');
		//$data['subject'] = $ticket->getSubject();
		// member gui tra loi
		if(isset($data['memberCode'])) {
			$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			$data['sender'] = $member->getEmail();
		} else {
			$data['sender'] = $ticket->getSender();
		}
		$this->setData($data)
			 ->setCreatedTime(now());
		//--------------------
   		// sent mail
		  if(isset($data['memberCode'])) { // member tra loi cho customer
		  	$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
		  	$to = $ticket->getSender();
			$mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
			$mailData['operator_name'] = $member->getName();
			$this->setName($mailData['operator_name']);
			$mailData['customer_name'] = $ticket->getName();
			//$mailData['customer_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			
			$base_url = $helper->_getBaseUrl($ticket->getStoreView());
			$mailData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			
			/*
			if($ticket->getStoreView() != '0')
			{
				$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
				$mailData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			}
			else{
				$mailData['customer_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
			}
			*/
			
		  	$mailData['message'] = $data['content'];
		  	$mailData['expired_time'] = Mage::getStoreConfig('helpdesk/config/complete');
	    	if(isset($data['file_attachment'])) {
				$mailData['file_attachment'] = $data['file_attachment'];
			}
			$department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
			$mailData['department_name'] = $department->getName();
			$templateId = $department->getReplyTicketCustomer();
		    if($templateId == '') {
	   			$storeId = Mage::app()->getStore()->getId();  
	   			$template = 'helpdesk/helpdesk_email_temp/reply_ticket_customer';
				$templateId = Mage::getStoreConfig($template,$storeId); 
	   		}
	   		
	   		$gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
	   		$from = $gateway->getEmail();
//	   		echo $from . '<br />' . $to . '<br />' . $templateId . '<br />';
//	   		echo'<pre>';var_dump($mailData);die();
			if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reply_ticket_customer',$storeId) == 1)
			{	   		
				Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
			}
			/* save ticket log */
			Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket->getId(), Mage::helper('helpdesk')->__('Staff Response'));
			/*	end save log	*/
		  } else {	//customer tra loi member
		  	$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			$to = $member->getEmail();
			$mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
			$mailData['operator_name'] = $member->getName();
			$mailData['customer_name'] = $ticket->getName();
			$this->setName($mailData['customer_name']);
			
			//$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			
			$base_url = $helper->_getBaseUrl($ticket->getStoreView());
			$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			
			/*
			if($ticket->getStoreView() != '0')
			{
				$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
				$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			}
			else{ 
				$mailData['operator_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			}
			*/
			
		    $mailData['message'] = $data['content'];
	    	if(isset($data['file_attachment'])) {
				$mailData['file_attachment'] = $data['file_attachment'];
			}
		  	if($ticket->getNote() != '') {
				$mailData['note'] = $ticket->getNote();
			}
			$department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
			$mailData['department_name'] = $department->getName();
			$templateId = $department->getReplyTicketOperator();
		    if($templateId == '') {
	   			$storeId = Mage::app()->getStore()->getId();  
	   			$template = 'helpdesk/helpdesk_email_temp/reply_ticket_operator';
				$templateId = Mage::getStoreConfig($template,$storeId); 
	   		}
	   		
	   		$gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
	   		$from = $gateway->getEmail();

			$storeId = Mage::app()->getStore()->getId();
			if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reply_ticket_operator',$storeId) == 1)
			{	   		
				Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
			}
			/* save ticket log */
			Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket->getId(), Mage::helper('helpdesk')->__('Customer Response'));
			/*	end save log	*/
		  }
		  //--------------------
                  
		  $this->save();	// save history
    }
}
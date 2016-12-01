<?php

class MW_HelpDesk_Model_Member extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/member');
    }
    
 	public function notifyStaff($note, $ticketId)
    {
    	$admin = Mage::getSingleton('admin/session')->getUser();
    	$nameAdmin = $admin->getFirstname() . ' ' . $admin->getLastname();
    	$ticket = Mage::getModel('helpdesk/ticket')->load($ticketId);	
    	    	
 		//save ticket
		$ticket->setNote($note);
		//------------------------
   		// sent noify ticket
			$member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			$to = $member->getEmail(); // gui den member
			$mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
			$mailData['message'] = $note;
			$mailData['operator_name'] = $member->getName();
			$mailData['staff_name'] = $nameAdmin;
			
			//$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
			
			$helper = Mage::helper('helpdesk/data');
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
			
			$department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
		  	$mailData['department_name'] = $department->getName();
			$templateId = $department->getInternalNoteNotification();
			$storeId = Mage::app()->getStore()->getId();  
			
		    if($templateId == '') {
	   			
	   			$template = 'helpdesk/helpdesk_email_temp/internal_note_notification';
				$templateId = Mage::getStoreConfig($template,$storeId); 
	   		}
			
		  	if($department->getDefaultGateway() == '') {
   				return 'Error config. Not exits DefaultGateway. Please contact admin.';
   			}
	   		$gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
	   		$from = $gateway->getEmail();

			if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_internal_note_notification',$storeId) == 1)
			{	   		
				Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
			}
		
		$ticket->save();
    }
    
    public function getMemberDataForAutocomplete(){
        $members = $this->getCollection();
        
        foreach($members as $member){
            $memberData[] = '\''.$member->getName().' [ '.$member->getEmail().' ]\'';
        }
        $returnData = '[';
        if(isset($memberData)) $returnData .= implode(',', $memberData);
        $returnData .= ']';
        return $returnData;
    } 
}
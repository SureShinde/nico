<?php

class MW_HelpDesk_Model_Ticketlog extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/ticketlog');
    }
    
    public function saveTicketLog($code_id, $customer_email='', $activity='', $staff_email='', $status)
    {
    	$this->setDateUpdate(now())
    		->setCodeId($code_id)
    		->setCustomerEmail($customer_email)
    		->setActivity($activity)
    		->setStaffEmail($staff_email)
    		->setStatus($status);
    	
    	$this->save();
    }
    
    public function saveEvents2Logs($ticket_id, $activity){
    	$ticket_log = Mage::getModel('helpdesk/ticketlog');
		$model_ticket = Mage::getModel('helpdesk/ticket')->load($ticket_id);
		$model_member = Mage::getModel('helpdesk/member')->load($model_ticket->getMemberId());
		$ticket_log->saveTicketLog($model_ticket->getCodeId(), 
								$model_ticket->getSender(), 
								$activity,
								$model_member->getEmail(),
								$model_ticket->getStatus()
								);
    }
    
	public function deleteTicketLog(){
        $expriedLine = Mage::getStoreConfig('helpdesk/config/delete_ticketlog');
        $expriedLine = strtotime('- '.$expriedLine.' days', Mage::getModel('core/date')->timestamp());
        $expriedTickets = $this->getCollection();
        $expriedTickets->addFieldToFilter('date_update', array('to' => date('Y-m-d H-i-s', $expriedLine)));
        foreach($expriedTickets as $expriedTicket){
            $ticketIds[] = $expriedTicket->getId();      
            $expriedTicket->delete();
        }
    }
    
	public function SaveLogsBySessionCustomer($ticket_id){
    
    	$arr_ticket_id = array();
		$arr_ticket_id = explode(",", Mage::getSingleton('customer/session')->getCountCustomerView());
		$key = in_array($ticket_id, $arr_ticket_id);
		if(!$key){
			Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket_id, Mage::helper('helpdesk')->__("Customer Viewing"));
		}
		Mage::getSingleton('customer/session')->setCountCustomerView(Mage::getSingleton('customer/session')->getCountCustomerView().','.$ticket_id);
    }
    
	public function SaveLogsByStaff($ticket_id){
   		Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket_id, Mage::helper('helpdesk')->__("Staff Viewing"));  
   	}
}
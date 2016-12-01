<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tab_History extends Mage_Adminhtml_Block_Template
{
	// Ticket data
	private $ticket;
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_helpdesk/view/tab/history.phtml');
        $this->ticket = Mage::getModel('helpdesk/ticket')->load(Mage::registry('ticket_data')->getData('ticket_id'));
    }
    
    public function getTicket()
    {
   		return $this->ticket;
    }
    
    public function getTicketHistories()
    {
        return Mage::getResourceModel('helpdesk/history_collection')
            				->addFieldToFilter('ticket_id', array('eq' => $this->ticket->getTicketId()))
            				->setOrder('history_id', 'DESC');
    }
    
    public function getTemplateData()
    {
    	$templates = Mage::getModel('helpdesk/template')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  
		return $templates;
    }
	
    public function getTicketId()
    {
    	return $this->getRequest()->getParam('id');
 
    }
}

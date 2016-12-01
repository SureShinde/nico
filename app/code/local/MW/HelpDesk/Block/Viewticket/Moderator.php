<?php
class MW_Helpdesk_Block_Viewticket_Moderator extends Mage_Core_Block_Template
{
	private $_collection;
	private $_ticket;
	
	public function __construct()
    {
       	$tickets = Mage::getModel('helpdesk/ticket')->getCollection()
				->addFilter('code_member',Mage::registry('current_ticket_code'));
		foreach ($tickets as $ticket) {
			$this->_ticket = $ticket;
			$ticketId = $ticket->getId();
		}
    	
        $this->_collection = Mage::getModel('helpdesk/history')->getCollection()
				->addFilter('ticket_id', $ticketId)
				->setOrder('history_id', 'DESC');
    }
    
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
	public function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'helpdesk_history.toolbar')
					   ->setCollection($this->getCollection());	// set data for navigation     
		$this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }
    
    public function getTicket()
    {
		return $this->_ticket;
    }
    
    public function getTicketId()
    {
        return $this->_ticket->getId();
    }
    
    public function getCodeMember()
    {
        return $this->_ticket->getCodeMember();
    }
    
    
    public function getDepartmentName()
    {
    	$department = Mage::getModel('helpdesk/department')->load($this->_ticket->getDepartmentId());
      	return $department->getName();
    }
    
    public function getDepartmentData()
    {
    	$departments = Mage::getModel('helpdesk/department')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  
      	return $departments;
    }
    
    public function getTemplateData()
    {
    	$templates = Mage::getModel('helpdesk/template')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  
		return $templates;
    }
    
    public function getMultiFileUploader(){
        return $this->getLayout()->createBlock('helpdesk/multipleuploader')->toHtml();
    }
    
   
}


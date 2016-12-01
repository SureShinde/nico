<?php
class MW_Helpdesk_Block_History extends Mage_Core_Block_Template
{
	private $_collection;
	
	public function __construct()
    { 
    	$ticketId = (int) $this->getRequest()->getParam('id');
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
    	$ticketId = (int) $this->getRequest()->getParam('id');
        return Mage::getModel('helpdesk/ticket')->load($ticketId);
    }
    
    public function getTicketId()
    {
        return (int) $this->getRequest()->getParam('id');
    }
    
    public function getDepartmentName()
    {
    	$department = Mage::getModel('helpdesk/department')->load($this->getTicket()->getDepartmentId());
      	return $department->getName();
    }
}
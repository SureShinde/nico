<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_Notification_Message extends Mage_Adminhtml_Block_Template
{
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_helpdesk/notification/message.phtml');
    }

    /**
     * Number ticket have got status is not solved
     * 
     * @return number
     */
    public function getMessageStatus()
    {
		$collection = Mage::getResourceModel('helpdesk/ticket_collection')->addFilter('untreated',2);
    	return count($collection);
    }
    
}

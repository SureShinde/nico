<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tab_Shareinfo extends Mage_Adminhtml_Block_Widget_Form
{
	// Ticket data
	private $ticket;
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_helpdesk/view/tab/shareinfo.phtml');
        $this->ticket = Mage::getModel('helpdesk/ticket')->load(Mage::registry('ticket_data')->getData('ticket_id'));
    }
    
    public function getTicket()
    {
   		return $this->ticket;
    }
    
	public function getTicketId()
    {
    	return $this->getRequest()->getParam('id');
 
    }
    
    public function getContentShare()
    {
    	$content_share = '';
    	$model_ticket =  Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
    	$sender = $model_ticket->getSender();
    	$model_share_info =  Mage::getModel('helpdesk/shareinfo')->getCollection()->addFieldToFilter('sender', $sender);
    	if(sizeof($model_share_info)>0){
    		foreach ($model_share_info as $collection) {
				$content_share = $collection->getShareInfo();
				break;
			}
    	}
    	return trim($content_share);
    }
}
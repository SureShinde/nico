<?php

class MW_HelpDesk_Model_Mysql4_Ticket_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/ticket');
    }
    
    /**
     *  Get all ticket posted by the customer
     * @param $customMail: Customer's email
     */
    public function getRelatedTicketByCustom($customMail, $ticketId){
        $this->addFieldToFilter('sender', array('like' => $customMail))
                ->addFieldToFilter('ticket_id', array('neq' => $ticketId));
        return $this;
    }
    
}
<?php

class MW_HelpDesk_Block_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        $this->setOrder(Mage::registry('current_order'));
        parent::_beforeToHtml();
    }

    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
	
	public function getOrderId(){
    	return Mage::registry('ticket_data')->getData('order');
    }
}

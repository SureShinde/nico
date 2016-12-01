<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2014
 */
/**
 * Agent admin edit form
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Chat
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * constructor
     * @access public
     * @return void
          */
    public function chatList(){	
       $agentId = $this->getRequest()->getParam('agentId');
       $customerId = $this->getRequest()->getParam('cid');	   
	   $collection = Mage::getModel('halox_salesagent/message')->getCollection();
       $collection->getSelect()->join(array('agents' => 'halox_sales_agents'), 'agents.id = main_table.sales_agents_id and main_table.sales_agents_id = "' . $agentId . '" and main_table.customer_id = "' .$customerId.'" ', array('agents.name','agents.image'))
                                ->limit(100);
	   return $collection;
	}
	 public function getCustomerDetail(){	       
       $customerId = $this->getRequest()->getParam('cid');	
	   $collection = Mage::getModel('customer/customer')->load($customerId);
	   return $collection;
	}
}

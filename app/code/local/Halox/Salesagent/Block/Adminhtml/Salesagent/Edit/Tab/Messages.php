<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Salesagent edit form tab
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Edit_Tab_Messages
    extends Mage_Adminhtml_Block_Widget_Form {
   
   public function getMessages(){
       $id = $this->getRequest()->getParam('id');
	   $collection = Mage::getModel('halox_salesagent/message')->getCollection();
       $collection->getSelect()->join(array('agents' => 'halox_sales_agents'), 'agents.id = main_table.sales_agents_id and main_table.sales_agents_id = "' . $id . '" ', array('agents.name','agents.image'))
                                ->limit(100);
	   return $collection;
   }
   public function getCustomerDetail($customerId){
      $customer = Mage::getModel('customer/customer')->load($customerId);
	  return $customer;
   }
   
   public function getLastMessages(){
   
	$id = $this->getRequest()->getParam('id');
	$resource = Mage::getSingleton('core/resource');
	
	$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' );
    $tableName = $resource->getTableName('halox_salesagent/message');
	
	$query = "
			SELECT m1.*
			FROM $tableName m1 LEFT JOIN $tableName m2
			 ON ((m1.customer_id = m2.customer_id AND m1.id < m2.id))
			WHERE m2.id IS NULL  AND m1.sales_agents_id = '".$id."' order by m1.id DESC
			LIMIT 0 , 100
		";
    return $result = $read->fetchAll($query);
	
   }
}

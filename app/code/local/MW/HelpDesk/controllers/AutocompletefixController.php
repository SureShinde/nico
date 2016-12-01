<?php
class MW_HelpDesk_AutocompletefixController extends Mage_Adminhtml_Controller_Action
{    
    protected function _isAllowed()
    {
        return true;
    }
    public function clientAction()
    {	
	    $q = strtolower($_GET["q"]);
		if (!$q) return;
	    $customers =  Mage::getModel('customer/customer')->getCollection()
	        		->addAttributeToFilter('email', array('like' => "%$q%"))
	        		->addAttributeToSelect('*');
	   	$result = array();
	   	foreach ($customers as $customer) {
	   		$result[$customer->getEmail()] = $customer->getName() . ' [' . $customer->getEmail() . ']';
	   	}
		foreach ($result as $key=>$value) {
			echo $value . "\n";
		}
		///////////////
//    	$orders = Mage::getModel('helpdesk/ticket')->getCollection();
//    	$orders->getSelect()
//          		->where('sender = ' . $q .')';
//	   	$result = array();
//	   	foreach ($orders as $order) {
//	   		$result[ $order->getOrder()] =  $order->getOrder();
//	   	}
//    	foreach ($result as $key=>$value) {
//			echo $value . "\n";
//		}
    }
    
	public function client1Action()
    {	
	    $q = strtolower($_GET["q"]);
		if (!$q) return;
		$orders = Mage::getModel('helpdesk/ticket')->getCollection();
    	$orders->getSelect()
				->where("sender = '" . $q ."'");
	   	$result = array();
	   	foreach ($orders as $order) {
	   		$result[ $order->getOrder()] =  $order->getOrder();
	   	}
    	foreach ($result as $key=>$value) {
			echo $value . "\n";
		}
    }
    
    public function orderAction()
    {		
    	$q = strtolower($_GET["q"]);
		if (!$q) return;
    	if($this->getRequest()->getParam('email')){
    		$collection = Mage::getResourceModel('sales/order_collection')
			->addAttributeToSelect('*')
			->addAttributeToFilter('customer_email', $this->getRequest()->getParam('email'))						
			->addAttributeToFilter('increment_id', array('like' => "%$q%"));
		} else {
			$collection = Mage::getResourceModel('sales/order_collection')
			->addAttributeToSelect('*')						
			->addAttributeToFilter('increment_id', array('like' => "%$q%"));
		}
	   	$result = array();
	   	foreach ($collection as $order) {
	   		$result[$order->getId()] = '#' .
	   			$order->getIncrementId() . " ( \${$order->getGrandTotal()} )" . ' on ' 
	   			. date('F jS, Y', Mage::getModel('core/date')->timestamp($order->getCreatedAt()));
	   	}
		foreach ($result as $key=>$value) {
			echo $value . "\n";
		}
    }
    
//    public function orderAction()
//    {		
//    	$q = strtolower($_GET["term"]);
//		if (!$q) return;
//		
//    	$collection = Mage::getResourceModel('sales/order_collection')
//			->addAttributeToSelect('*')
//			->addAttributeToFilter('customer_email', $this->getRequest()->getParam('email'))						
//			->addAttributeToFilter('increment_id', array('like' => "%$q%"));
//
//		$result = '[';
//	   	$count = 1;
//	   	foreach ($collection as $order) {
//	   		if ($count == sizeof($collection)) {
//	   			$result .= '{ "id": "'.$count.'", "label": "#' 
//	   					. $order->getIncrementId() . " (\${$order->getGrandTotal()})" . ' on ' . date('F jS, Y', Mage::getModel('core/date')->timestamp($order->getCreatedAt())) 
//	   					. '", "value": "'.$order->getId().'" }';
//				break;
//	   		}
//	    	$result .= $order->getIncrementId();
//			$count++;
//	   	}
//	   	$result .= ']';
//	   	echo $result;
//    }
    
}
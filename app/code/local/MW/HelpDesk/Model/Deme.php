<?php

class MW_HelpDesk_Model_Deme extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/deme');
    }
//    
//   	public function direct($orderData)
//    {
//    	$collection = $this->getCollection();
//    	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
//    	$sql = 'UPDATE ' . $collection->getTable('mw_department_member') 
//    	     . 'SET department_id =' 
//    	     . $a;
//		$results = $conn->fetchAll($sql);
//		foreach($results as $row) {
//		    echo $row['name'] . "\n";
//		}
//    }
}
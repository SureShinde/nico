<?php

class MW_HelpDesk_Block_Adminhtml_Statistic extends Mage_Core_Block_Template {
    private $memberStatic = null;
    private $departmentStatic = null;
    private $tabs = null;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('mw_helpdesk/statistic.phtml');
    }

    public function collectionDepartment() {
        $collection = Mage::getModel('helpdesk/department')->getCollection();
        $collection->getSelect()->join(array('ticket_table' => $collection->getTable('helpdesk/ticket')), 'main_table.department_id = ticket_table.department_id', array('total' => 'count(*)'))
            ->group('ticket_table.department_id');
        return $collection;
//        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
//        $sql = 'SELECT COUNT(mw_departments.name) as total, 
//    				   mw_departments.name as name, mw_departments.department_id as id
//    						FROM mw_tickets, mw_departments
//    					 		WHERE mw_tickets.department_id = mw_departments.department_id
//    					 			GROUP BY mw_tickets.department_id';
//        return $write->query($sql);
    }

    public function countOpenTicketDepartment() {
        if(is_null($this->departmentStatic)){
            $this->prepareTicketStatic();
        }
        
        return $this->departmentStatic;
//        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
//        $sql = "SELECT COUNT(status) as count, status
//    						FROM mw_tickets
//    					 		WHERE department_id = {$id}
//    					 			GROUP BY status";
//    	$result = $write->query($sql);
//    	foreach ($result as $_result) {
//    		Zend_Debug::dump($_result);
//    	}
//        return $write->query($sql);
//        echo($collection->getSelect());die;
        
    }

    public function collectionOperaror() {
        $collection = Mage::getModel('helpdesk/member')->getCollection();
        $collection->getSelect()->join(array('ticket_table' => $collection->getTable('helpdesk/ticket')), 'main_table.member_id = ticket_table.member_id', array('total' => 'count(*)'))
                ->group('main_table.member_id');
        //  ->groupByAttribute('ticket_table.member_id');
//    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
//    	$sql = 'SELECT COUNT(.name) as total, 
//    				   mw_members.name as name, mw_members.member_id as id
//    						FROM mw_tickets, mw_members
//    					 		WHERE mw_tickets.member_id = mw_members.member_id
//    					 			GROUP BY mw_tickets.member_id';
//		return $write->query($sql);
        return $collection;
    }

    public function countOpenTicketOperator() {
//        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
//        $sql = "SELECT COUNT(status) as count, status
//    						FROM mw_tickets
//    					 		WHERE member_id = {$id}
//    					 			GROUP BY status";
//        return $write->query($sql);
//        echo($collection->getSelect());die;
        if(!is_null($this->memberStatic)){
            $this->prepareTicketStatic();
        }
        
        return $this->memberStatic;
    }

    public function tags() {
        if(is_null($this->tabs)){
            $collection = Mage::getModel('helpdesk/tag')->getCollection();
            $tags = array();
            foreach ($collection as $tag) {
                if (!in_array($tag->getName(), $tags)) {
                    $tags[] = $tag->getName();
                }
            }
            $this->tabs = $tags;
        }
        return $this->tabs;
        
//        $tags = array();
//        $collection = Mage::getModel('helpdesk/tag')->getCollection();
//        foreach ($collection as $tag) {
//            if (!in_array($tag->getName(), $tags)) {
//                $tags[] = $tag->getName();
//            }
//        }
//        return $tags;
    }
    
	
	
    public function prepareTicketStatic(){
        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
                ->addFieldToSelect(array('member_id', 'department_id', 'status'))
                ->addExpressionFieldToSelect('count', 'count(*)', null);
        $collection->getSelect()->group('member_id', 'department_id', 'status');
        $memberStatic = array();
        $departmentStatic = array();
        foreach($collection as $ticketStatic){
            if(isset($memberStatic[$ticketStatic->getMemberId()][$ticketStatic->getStatus()])){
                $memberStatic[$ticketStatic->getMemberId()][$ticketStatic->getStatus()] += $ticketStatic->getCount();
            }else{
                $memberStatic[$ticketStatic->getMemberId()][$ticketStatic->getStatus()] = $ticketStatic->getCount();
            }
            if(isset($departmentStatic[$ticketStatic->getDepartmentId()][$ticketStatic->getStatus()])){
                $departmentStatic[$ticketStatic->getDepartmentId()][$ticketStatic->getStatus()] += $ticketStatic->getCount();
            }else{
                $departmentStatic[$ticketStatic->getDepartmentId()][$ticketStatic->getStatus()] = $ticketStatic->getCount();
            }           
        }
        
        $this->memberStatic = $memberStatic;
        $this->departmentStatic = $departmentStatic;
    }

	public function StatisticsByDepartmentStatus($department_id, $status){
    	$collection = Mage::getModel('helpdesk/ticket')->getCollection()
    	 			->addFieldToFilter('department_id', $department_id)
    	 			->addFieldToFilter('status', $status);
    	return sizeof($collection);
    }
    
	public function StatisticsByMemberStatus($member_id, $status){
    	$collection = Mage::getModel('helpdesk/ticket')->getCollection()
    	 			->addFieldToFilter('member_id', $member_id)
    	 			->addFieldToFilter('status', $status);
    	return sizeof($collection);
    }
}

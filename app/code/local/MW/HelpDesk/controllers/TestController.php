<?php
class MW_HelpDesk_TestController extends Mage_Core_Controller_Front_Action
{
    
     protected function _isAllowed()
    {
        return true;
    }
	public function regexAction()
    {
		/*
		$helper = Mage::helper('helpdesk/data');
		echo 'URl: ' . $helper->_getDefaultBaseUrl() . '<br />';
		$iDefaultStoreId = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
		$base_url = Mage::getUrl('', array('_store' => $iDefaultStoreId));
		echo 'Default URL: ' . $base_url;
		die;
		*/
		//$ticket = Mage::getModel('helpdesk/ticket')->load(60);
		//$ticket->setStoreView(3)->setTicketId(221);
		//echo 'Gia tri: ' . Mage::getModel('helpdesk/ticket')->deleteExpiredTicket();
    //delete ticket log
    echo Mage::getDesign()->getSkinUrl('mw_helpdesk/images/button_cancel.png',array('_area'=>'frontend'));
    die;
    foreach (Mage::getModel('helpdesk/department')->getCollection()->addFieldToFilter('active', array('eq' => 1))->setOrder('department_sort_order', 'asc') as $department):
    	echo $department->getName(). '<br />';
    endforeach;
    	die;
     $today = date('w', time());
     echo 'Today: ' . $today;


    	$store_date = Mage::app()->getLocale()->getOptionWeekdays();
    	Zend_Debug::dump($store_date);
    	foreach ($store_date as $d) {
    		echo $d['label'] . '<br />';
    	}
    	echo $store_date[0];
    	die;
    	if (Mage::getStoreConfig('helpdesk/config/delete_ticketlog')>0 && Mage::getStoreConfig('helpdesk/config/delete_ticketlog') != '') {
            echo 'true';
        } else {
            echo 'false';
        }
		die;
		Zend_debug::dump(Mage::getModel('helpdesk/ticket')->load(60));
		die;
		$storeId = Mage::app()->getStore()->getId();
			//*** 1
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/new_ticket_customer',$storeId) . '<br />';
			
			//*** 2
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/reply_ticket_customer',$storeId) . '<br />';
			
			//*** 3
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/new_ticket_operator',$storeId) . '<br />';

			//*** 4
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/reply_ticket_operator',$storeId) . '<br />';
			
			//*** 5
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/reassign_ticket_operator',$storeId) . '<br />';
			
			//*** 6
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/reply_ticket_operator',$storeId) . '<br />';
			
			echo Mage::getStoreConfig('helpdesk/helpdesk_email_temp/internal_note_notification',$storeId);
			die;
		echo 'abc';
		$members = Mage::getModel('helpdesk/ticket')->getTicketIds2Next();
		Zend_debug::dump($members);
		//die;
		$members = Mage::getModel('helpdesk/ticket')->load(481);
		Zend_debug::dump($members);
		die;
	}
	
	public function testCronAction()
    {
    	echo 'run Cron ...<br />';
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $db->query('select * from core_resource where code like \'%helpdesk%\'');
        
        var_dump($result->fetch(PDO::FETCH_ASSOC));
    }
	public function updateAction()
	{
		Mage::dispatchEvent('click_ticket_id',array('ticket_id'=> $this->getRequest()->getParam('isajax')));
	}
	public function indexAction()
    {
    		$members = Mage::getModel('helpdesk/deme')->getCollection();
    		$members->getSelect()->join('mw_members',
                   	'main_table.member_id = mw_members.member_id',
                    array('name'=>'mw_members.name','email'=>'mw_members.email') 
                   )->join('mw_departments',
                   	'main_table.department_id = mw_departments.department_id',
                    array('department_name' =>'mw_departments.name') 
                   ); 
           echo $members->getSelect();
	   	//foreach ($members as $member) {
			//Zend_Debug::dump($member->getData());
	   	//}
	   
//	    $tickets = Mage::getModel('helpdesk/ticket')->getCollection();
//		foreach($tickets as $_ticket) {
//			$ticket = Mage::getModel('helpdesk/ticket')->load($_ticket->getId());
//			$subject = $ticket->getSubject();
//			echo $subject . '<br />';
//			echo trim(substr($subject, strpos($subject, '-')+1)). '<br />';
//			echo trim(substr($subject, 0, strpos($subject, '-'))). '<hr />';
//			$ticket->setSubject(trim(substr($subject, strpos($subject, '-')+1)));
//			$ticket->setCodeId(trim(substr($subject, 0, strpos($subject, '-'))));
//			$ticket->save();
//		}
    }
    
    

}
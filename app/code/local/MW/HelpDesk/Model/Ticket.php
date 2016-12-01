<?php
 
class MW_HelpDesk_Model_Ticket extends Mage_Core_Model_Abstract {
    const INCREMENT_NUMBER = 1000000;

    public function _construct() {
        parent::_construct();
        $this->_init('helpdesk/ticket');
    }

    /**
     * Tra lai trang thai cua ticket la co bi miss hay ko
     * 
     * @param string $getLastReplyTime
     * @param string $getStepReplyTime
     * @return bool
     */
    public function missedTicket($getLastReplyTime, $getStepReplyTime) {
        $now = time();
        $noticeTime = (int) Mage::getStoreConfig('helpdesk/config/notice');
        $stepNoticeTime = (int) Mage::getStoreConfig('helpdesk/config/step_notice');
        $noticeTime = $noticeTime * 60 * 60;
        $stepNoticeTime = $stepNoticeTime * 60 * 60;

        $lastReplyTime = strtotime($getLastReplyTime);
        $diff = $now - $lastReplyTime;

        $stepReplyTime = strtotime($getStepReplyTime);
        $diff2 = $now - $stepReplyTime;

        if (($noticeTime > 0 && $diff > $noticeTime && $lastReplyTime > $stepReplyTime)
                || ($stepNoticeTime > 0 && $diff2 > $stepNoticeTime && $lastReplyTime < $stepReplyTime)) {
            return true;
        }
        return false;
    }

    public function _getLastTicketId() {
        $collection = $this->getCollection()->addOrder('created_time', 'DESC');
        foreach ($collection as $ticket) {
            return $ticket->getId();
        }
        return 0;
    }

    /*
     * sent reassign ticket, 
     * @param $id  
     */

    public function reassignTicket($id) {
		$storeId = Mage::app()->getStore()->getId();
        $ticket = $this->load($id);
        $mailData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
        $mailData['message'] = $ticket->getContent();
        //$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
		
		//get store_view ticket
		$helper = Mage::helper('helpdesk/data');
		$base_url = $helper->_getBaseUrl($ticket->getStoreView());
		$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();

		/*
		if($ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
			$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
		}
		else{
			$mailData['operator_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
		}
		*/
		
        if ($ticket->getFileAttachment() != '') {
            $mailData['file_attachment'] = $ticket->getFileAttachment();
        }
        if ($ticket->getNote() != '') {
            $mailData['note'] = $ticket->getNote();
        }
		        
        $member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
        
        $to = $member->getEmail(); // gui dem member moi
        $mailData['operator_name'] = $member->getName();
        $mailData['customer_name'] = $ticket->getName();
        $department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
        $mailData['department_name'] = $department->getName();
        $templateId = $department->getReassignTicketOperator();
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/reassign_ticket_operator';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
		//$templateId = str_replace("helpdesk_email_temp", "config", $templateId);
        $gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
        $from = $gateway->getEmail();
        if (is_null($from)) {
            echo 'Department not exists Default Gateway';
            die();
        }

        if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reassign_ticket_operator',$storeId) == 1)
        {
        	Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
        }
    }

    /*
     * Admin reply ticket
     * 
     * $replyBy : 1 => customer
     * 			: 2 => operator
     */

    public function replyTicketFromAdmin($historyData, $replyBy = 2) {
        //Zend_Debug::dump(Mage::getSingleton('admin/session')->getUser());die();
		$storeId = Mage::app()->getStore()->getId();
		/*
        $admin = Mage::getSingleton('admin/session')->getUser();
        $emailAdmin = $admin->getEmail();
        $nameAdmin = $admin->getFirstname() . ' ' . $admin->getLastname();
        $ticket = $this->load($historyData['ticket_id']);
		*/
		/* save ticket log */
        Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($historyData['ticket_id'], Mage::helper('helpdesk')->__("Staff Response"));
        
		$ticket = $this->load($historyData['ticket_id']);
		$emailAdmin = ''; $nameAdmin = ''; 
		$admin = Mage::getSingleton('admin/session')->getUser();
		if (isset($admin)) {			
			$emailAdmin = $admin->getEmail(); 
			$nameAdmin = $admin->getFirstname() . ' ' . $admin->getLastname();
		}else{  
			$model_member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
			if(sizeof($model_member) > 0){
				$emailAdmin = $model_member->getEmail(); 
				$nameAdmin = $model_member->getName(); 
			}
			else {
				$model_department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
				if(sizeof($model_department->getMemberId()) > 0){
					$model_member = Mage::getModel('helpdesk/member')->load($model_department->getMemberId());
					if(sizeof($model_member) > 0){
						$emailAdmin = $model_member->getEmail(); 
						$nameAdmin = $model_member->getName();
					}
					else{
            			$emailAdmin = Mage::getStoreConfig('helpdesk/client_config/default_sender', $storeId);
            			$nameAdmin = Mage::getStoreConfig('helpdesk/client_config/default_sender', $storeId);
					}
				}
			}
		}

        $order = Mage::getModel('sales/order')->load($ticket->getOrder());
        //save history
        $history = Mage::getModel('helpdesk/history');
        $history->setData($historyData)
                ->setMemberId($ticket->getMemberId())
                ->setDepartmentId($ticket->getDepartmentId())
                ->setSender($emailAdmin)
                ->setCreatedTime(now());


        //save ticket
        $ticket->setLastReplyTime(now())
                ->setReplyBy($replyBy);
        if ($historyData['no_change_status'] == 2) {
            $ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING);
        }
        //------------------------
        // sent reply ticket
        $historyData['subject'] = 'Re: ' . $ticket->getCodeId() . ' - ' . $ticket->getSubject();
        $historyData['message'] = $historyData['content'];
        $historyData['order_id'] = $order->getIncrementId();
        $to = $ticket->getSender();
        $member = Mage::getModel('helpdesk/member')->load($ticket->getMemberId());
//		$historyData['operator_name'] = $member->getName();	//
        $historyData['operator_name'] = $nameAdmin;
        $history->setName($historyData['operator_name']);  
        
        $historyData['customer_name'] = $ticket->getName();
        $historyData['expired_time'] = Mage::getStoreConfig('helpdesk/config/complete');
		
        //$historyData['customer_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();

		$helper = Mage::helper('helpdesk/data');
		$base_url = $helper->_getBaseUrl($ticket->getStoreView());
		$historyData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
		/*
		if($ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $ticket->getStoreView()));
			$historyData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
		}
		else{
			$historyData['customer_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . $ticket->getCodeCustomer();
		}
		*/
		
        $department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
        $historyData['department_name'] = $department->getName();
        $templateId = $department->getReplyTicketCustomer();
		
        // --- 4. Add Emailreference ID to template varialbe
        $historyData['email_ref_id'] = $ticket->getEmailRefId();
		$historyData['ticket_id'] = $ticket->getCodeId();
		
        /*** set for show in backend ticket ***/
        $content = $historyData['content'];
        if(strpos($content, "{{var customer_name}}"))
        	$content = str_replace("{{var customer_name}}", $historyData['customer_name'], $content);
        	
        if(strpos($content, "{{var order_id}}"))
			$content = str_replace("{{var order_id}}", $historyData['order_id'], $content);	
				
        if(strpos($content, "{{var subject}}"))
        	$content = str_replace("{{var subject}}", $historyData['subject'], $content);

        if(strpos($content, "{{var department_name}}"))
        	$content = str_replace("{{var department_name}}", $historyData['department_name'], $content);
        	
        if(strpos($content, "{{var operator_name}}"))
        	$content = str_replace("{{var operator_name}}", $historyData['operator_name'], $content);	
        	
        if(strpos($content, "{{var customer_direct_link}}"))
        	//$content = str_replace("{{var customer_direct_link}}", $historyData['customer_direct_link'], $content);	
			$content = str_replace("{{var customer_direct_link}}", '<a href="' . $historyData['customer_direct_link'] . '">' . $historyData['customer_direct_link'] . '</a>', $content);

        if(strpos($content, "{{var expired_time}}"))
        	$content = str_replace("{{var expired_time}}", $historyData['expired_time'], $content);
        
		if(strpos($content, "{{var email_ref_id}}"))
        	$content = str_replace("{{var email_ref_id}}", $historyData['email_ref_id'], $content);
		
        if(strpos($content, "{{var ticket_id}}"))
        	$content = str_replace("{{var ticket_id}}", $historyData['ticket_id'], $content);
			
//        if(strpos($content, "{{var operator_direct_link}}"))
//        	$content = str_replace("{{var operator_direct_link}}", $historyData['operator_direct_link'], $content);        	
//        if(strpos($content, "{{var late}}"))
//        	$content = str_replace("{{var late}}", $historyData['late'], $content);	
//       	if(strpos($content, "{{var staff_name}}"))
//        	$content = str_replace("{{var staff_name}}", $historyData['staff_name'], $content);
//        if(strpos($content, "{{var note}}"))
//        	$content = str_replace("{{var note}}", $historyData['note'], $content);	
        	
        $history->setContent($content);	
        	
        	
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/reply_ticket_customer';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
        try {
            $gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
            $from = $gateway->getEmail();
        } catch (Exception $e) {
            echo 'Department not exists Default Gateway';
        }

		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_reply_ticket_customer',$storeId) == 1)
		{
			Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $historyData);
		}

        $history->save();
        $ticket->save();
    }

    /*
     * create ticket for Account Submit Form & Contact Form & admin (stand)
     * 
     * $replyBy : 1 => customer
     * 			: 2 => operator
     */

    public function saveTicket($data, $replyBy = 1) {
        //var_dump($data);die();
        $codeMember = '';
        $memberId = '';
		$storeId = Mage::app()->getStore()->getId();
		$this->setData($data);
		$insertId = $this->save()->getId();
        // ton tai moderator nghia la ticket dc tao tu form admin
        if (isset($data['moderator'])) {
            $department = Mage::getModel('helpdesk/department')->load($data['department_id']);
            //$lastTicketId = $this->_getLastTicketId();
            $lastTicketId = $insertId;
            //$incrementId = self::INCREMENT_NUMBER + $lastTicketId + 1;
            $incrementId = self::INCREMENT_NUMBER + $lastTicketId;
            $subject = $department->getDcode() . '#' . $incrementId . ' - ' . $data['subject'];
            $data['code_id'] = $department->getDcode() . '#' . $incrementId;
            $this->setData($data);
            // gan ticket cho department hoac operator
            if ($data['moderator'] != '') {
                $members = Mage::getModel('helpdesk/member')->getCollection()
                        ->addFieldToFilter('email', array('eq' => $data['moderator']));
                foreach ($members as $member) {
                    $memberId = $member->getId();
                    $codeMember = md5($memberId . $lastTicketId . rand(5, 15));
                    $this->setMemberId($memberId)
                            ->setCodeMember($codeMember);
                }
                // xu ly loi neu operator khong thuoc department chi ra
                $collection = Mage::getModel('helpdesk/department')->getCollection()
                        ->addFieldToFilter('main_table.department_id', array('eq' => $data['department_id']));
                $collection->getSelect()->join('mw_department_member', 'main_table.department_id = mw_department_member.department_id', array('asdasd' => 'mw_department_member.department_id')
                        )->join('mw_members', 'mw_department_member.member_id = mw_members.member_id', array('bbbbb' => 'mw_members.member_id')
                        )
                        ->where("mw_members.email='" . $data['moderator'] . "'");
                if (sizeof($collection) <= 0) {
                    return 'Member must along to department';
                }
            } else { // lay truong nhom
                $memberId = $department->getMemberId();
                $codeMember = md5($memberId . $lastTicketId . rand(5, 15));
                $this->setMemberId($memberId)
                        ->setCodeMember($codeMember);
            }
        } else { // tixket tao tu Account Submit Form & Contact Form
            // neu ko ton tai department, tuc nguoi dung ko dc chon department khi sent
            // su dung department mac dinh
            if (!isset($data['department_id'])) {
                $data['department_id'] = Mage::getStoreConfig('helpdesk/client_config/default_department');
                if ($data['department_id'] == '') {
                    return 'Error config. Not exits department default. Please contact admin.';
                }
            }
            $department = Mage::getModel('helpdesk/department')->load($data['department_id']);
            //$lastTicketId = $this->_getLastTicketId();
            $lastTicketId = $insertId;
            //$incrementId = self::INCREMENT_NUMBER + $lastTicketId + 1;
            $incrementId = self::INCREMENT_NUMBER + $lastTicketId;
            $subject = $department->getDcode() . '#' . $incrementId . ' - ' . $data['subject'];
            $data['code_id'] = $department->getDcode() . '#' . $incrementId;

            // save demorator tuong ung voi department( 1 department luon co 1 demorator )
            $memberId = $department->getMemberId();
            $codeMember = md5($memberId . $lastTicketId . rand(5, 15));
            $this->setData($data)
                    ->setMemberId($memberId)
                    ->setCodeMember($codeMember);
        }

        $codeCustomer = md5($data['sender'] . $lastTicketId . rand(5, 15));
        $this->setCreatedTime(now())
                ->setLastReplyTime(now())
                ->setCodeCustomer($codeCustomer)
                ->setReplyBy($replyBy);
        if (!isset($data['priority'])) {
            $this->setPriority(MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_NORMAL);
        }
        if (!isset($data['status'])) {
            $this->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN);
			//$this->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW); 
        }
        //$insertId = $this->save()->getId();
        $this->setTicketId($insertId);  
        $this->save();   
        //------------------------
        // customer sent mail(new ticket from form)
        $member = Mage::getModel('helpdesk/member')->load($memberId); // truong nhom
        $to = $member->getEmail(); // gui den truong nhom
        $mailData['subject'] = trim($subject);
        $mailData['message'] = $data['content'];
		//Mage::log($data['content']);
        if (isset($data['file_attachment'])) {
            $mailData['file_attachment'] = $data['file_attachment'];
        }
        $mailData['operator_name'] = $member->getName();
        $mailData['customer_name'] = $data['name'];
		//Mage::log("Customer name1: " . $data['name']);
        $mailData['department_name'] = $department->getName();
		
		//get store_view ticket
		$md_ticket = Mage::getModel('helpdesk/ticket')->load($insertId);
		$helper = Mage::helper('helpdesk/data');
		$base_url = $helper->_getBaseUrl($md_ticket->getStoreView());
		$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		
		/*
		if($md_ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $md_ticket->getStoreView()));
			$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		}
		else{
			$mailData['operator_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		}
		*/
		
        $templateId = $department->getNewTicketOperator(); // customer sent
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/new_ticket_operator';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
        if ($department->getDefaultGateway() == '') {
            return 'Error config. Not exits DefaultGateway. Please contact admin.';
        }
        $gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
        $from = $gateway->getEmail(); // email tuong ung

		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_new_ticket_operator',$storeId) == 1)
		{ 
        	Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
		}
        //------------------------
        // sent response for customer
        $to = $data['sender'];
        $mailResponseData['customer_name'] = $data['name'];
        $mailResponseData['subject'] = trim($subject);
        $mailResponseData['message'] = $data['content'];
        $mailResponseData['department_name'] = $department->getName();
		
		$base_url = $helper->_getBaseUrl($md_ticket->getStoreView());
		$mailResponseData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
		
		/*
		if($md_ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $md_ticket->getStoreView()));
			$mailResponseData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
        }
		else{
			$mailResponseData['customer_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
		}
		*/
		
		$templateId = $department->getNewTicketCustomer(); // response customer
		
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/new_ticket_customer';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_new_ticket_customer',$storeId) == 1)
		{ 
        	Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailResponseData);
		}
        //------------------------
        //add tags
        if (!empty($data['tags'])) {
            $tags = $temp = array();
            $temps = explode(',', $data['tags']);
            foreach ($temps as $temp) {
                $temp = trim($temp);
                if (!empty($temp)) {
                    $tag = Mage::getModel('helpdesk/tag');
                    $tag->setTicketId($insertId)
                            ->setName($temp)
                            ->save();
                }
            }
        }
    }

    /*
     * create ticket for Gateway
     * 
     * $replyBy : 1 => customer
     * 			: 2 => operator
     */

    public function saveTicketFromGateway($data, $replyBy = 1) {
        //var_dump($data);die();
        // tim department mac dinh cua gateway
		$storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('helpdesk/gateway')->getCollection()
                ->addFieldToFilter('email', array('eq' => $data['to']));
        foreach ($collection as $gateway) {
            $data['department_id'] = $gateway->getDefaultDepartment();
        }
		
		//------------  pare content leter of email ebay to remove junk  ------------
		/*
		$helper = Mage::helper('helpdesk/data');
		if($helper->isEbayMail($data['sender'])>0){
			$content = explode("Dear", $data['content']);
			//Mage::log('Size of content: ' . $content[1]);
			
			$regex_pattern2 = "/\s+\-\S+/"; // get -{username}
			preg_match($regex_pattern2, $content[1], $matches2);
			$content1 = explode($matches2[0], $content[1]);
			$newcont = "Dear " . $content1[0] . $matches2[0];
			//Mage::log('Content new: ' . $newcont);
			//Mage::log('the end ' . $helper->isEbayMail($data['sender']));
		}
		else {
			$newcont = $data['content'];
		}
		*/
		//------------  end pare content leter of email ebay to remove junk  ------------
		
        $department = Mage::getModel('helpdesk/department')->load($data['department_id']);
		/*** get Store view department ***/
		$store_view = '0';
		$arr_stores = explode(",", $department->getStores());
		if(in_array('0', $arr_stores) ) $store_view = '0';
		else{
			$store_view = $arr_stores[0];
		}
		
		/* fix when subject contain specical character */
		$data['subject'] = mb_convert_encoding($data['subject'], 'UTF-8');
		
        $this->setData($data)
                ->setCreatedTime(now())
                ->setLastReplyTime(now())
                ->setPriority(MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_NORMAL)
                ->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW)
                ->setReplyBy($replyBy)
				->setStoreView($store_view);
				//->setContent($newcont); // comment here to cut mail messager 

		$insertId = $this->save()->getId();	//get lastest ticket id after save ticket data
		
		//*** save code_id of ticket again
		//$lastTicketId = $this->_getLastTicketId();
		$lastTicketId = $insertId;
        $incrementId = self::INCREMENT_NUMBER + $lastTicketId;
		
		$subject = $department->getDcode() . '#' . $incrementId . ' - ' . $data['subject'];
		
        $data['code_id'] = $department->getDcode() . '#' . $incrementId;
        $this->setCodeId($data['code_id']);
        
        
        // save demorator tuong ung voi department (1 department luon co 1 demorator)
        $memberId = $department->getMemberId();
        $codeMember = md5($memberId . $lastTicketId . rand(5, 15));
        $this->setMemberId($memberId)
                ->setCodeMember($codeMember);

        $codeCustomer = md5($data['sender'] . $lastTicketId . rand(5, 15));
        $this->setCodeCustomer($codeCustomer);
		
        //------------------------	
        // sent new ticket from form
        $member = Mage::getModel('helpdesk/member')->load($memberId); // truong nhom
        $to = $member->getEmail(); // gui den truong nhom
		//Mage::log('Data: ', $data);
        $mailData['subject'] = $subject;
        $mailData['message'] = $data['content'];
        if (isset($data['file_attachment'])) {
            $mailData['file_attachment'] = $data['file_attachment'];
        }
        $mailData['operator_name'] = $member->getName(); // ten truong nhom
        $mailData['customer_name'] = $data['name'];
		
		
        $mailData['department_name'] = $department->getName();
		
        //$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $codeMember;
        //*** get store_view 
		
		$md_ticket = Mage::getModel('helpdesk/ticket')->load($lastTicketId);
		$helper = Mage::helper('helpdesk/data');
		
		$base_url = $helper->_getBaseUrl($md_ticket->getStoreView());
		$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		
		/*
		if($md_ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $md_ticket->getStoreView()));
			$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		}
		else{
			$mailData['operator_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $codeMember;
		}
		*/
		
		$templateId = $department->getNewTicketOperator(); // customer sent
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/new_ticket_operator';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
        if ($department->getDefaultGateway() == '') {
            return 'Error config. Not exits DefaultGateway. Please contact admin.';
        }
        $gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
        $from = $gateway->getEmail(); // email tuong ung

        if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_new_ticket_operator',$storeId) == 1)
		{
        	Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailData);
		}
        
        // sent response for customer
        $to = $data['sender'];
        $mailResponseData['subject'] = trim($subject);
        $mailResponseData['message'] = $data['content'];
        $mailResponseData['customer_name'] = $data['name'];
	
        $mailResponseData['department_name'] = $department->getName();
        //$mailResponseData['customer_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
        
		$base_url = $helper->_getBaseUrl($md_ticket->getStoreView());
		$mailResponseData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
		
		//*** get store_view 
		/*
		if($md_ticket->getStoreView() != '0')
		{
			$base_url = Mage::getUrl('', array('_store' => $md_ticket->getStoreView()));
			$mailResponseData['customer_direct_link'] = $base_url . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
        }
		else{
			$mailResponseData['customer_direct_link'] = $helper->_getDefaultBaseUrl() . 'helpdesk/viewticket/customer/code/' . $codeCustomer;
		}
		*/
		
		$templateId = $department->getNewTicketCustomer(); // response customer
		
        if ($templateId == '') {
            $storeId = Mage::app()->getStore()->getId();
            $template = 'helpdesk/helpdesk_email_temp/new_ticket_customer';
            $templateId = Mage::getStoreConfig($template, $storeId);
        }
		if(Mage::getStoreConfig('helpdesk/helpdesk_email_temp/enabled_new_ticket_customer',$storeId) == 1)
		{
			Mage::getModel('helpdesk/email')->sentMail($from, $to, $templateId, $mailResponseData);
		}
        //------------------------
        //$this->save();
        $this->setTicketId($insertId);  
        $this->save();
        
         /* save ticket log */
		Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($this->getId(), Mage::helper('helpdesk')->__('Creating New Ticket'));
		//------------------------
		
		//*** process for email ebay
     	$helper = Mage::helper('helpdesk/data');
        $ticketCollections = Mage::getModel('helpdesk/ticket')->getCollection()->setOrder('ticket_id', 'DESC');
		foreach ($ticketCollections as $value) {
			if($helper->isEbayMail($value->getSender())>0){
				//save item id
				//$str = $value->getContent();
				$str = $data['content'];
	//			$item_id = $helper->getItemIdFromEmailEbay($str);
	//			$buyer = $helper->getBuyerFromEmailEbay($str);
	//			$email_ref_id = $helper->getEmailRefIdFromEmailEbay($str);

				$item_id = trim(str_replace("&nbsp;","",$helper->getItemIdFromEmailEbay($str)));
				$item_id = trim(str_replace("<br />","",$item_id));
				$buyer = trim(str_replace("&nbsp;","",$helper->getBuyerFromEmailEbay($str)));
				//$buyer = trim(str_replace("<br />","",$buyer));
				$regex_pattern2 = "/(\d+)/";
				preg_match($regex_pattern2, $item_id, $matches2);
				$item_id = $matches2[0];
				
				if(strpos($buyer, " ")>0){
					$regex_pattern3 = "/(\w+)(\s)/";
					preg_match($regex_pattern3, $buyer, $matches3);
					$buyer = trim($matches3[0]);
				}
				
				$email_ref_id = $helper->getEmailRefIdFromEmailEbay($str);
			
			//if($helper->isEbayMail($value->getSender())>0){
				$this->setItemId($item_id)->setTicketId($value->getId());          
				$this->save();  
				$this->setBuyerUsername($buyer)->setTicketId($value->getId());   
				$this->save();         
				$this->setEmailRefId($email_ref_id)->setTicketId($value->getId());                   		    	
                $this->save();
			} 
			break;
		}
		/*** --------------------------------- */
		
		/* -------------------- check condition rule -------------------- */
		$model_ticket_rules = Mage::getModel('helpdesk/rules');
		//when ticket created from fronend
		$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('created',$this->getId(), 1);
		//echo $list_ruleids;die;
		//------------------------
    }
    
    public function deleteExpiredTicket(){
    	//echo 'chay expire';die;
        $expriedLine = Mage::getStoreConfig('helpdesk/config/expried_time');
        
        $expriedLine = strtotime('- '.$expriedLine.' days', Mage::getModel('core/date')->timestamp());
        $expriedTickets = $this->getCollection();
        $expriedTickets->addFieldToFilter('last_reply_time', array('to' => date('Y-m-d H-i-s', $expriedLine)))
                ->addFieldToFilter('status', array('eq' => MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED));
        foreach($expriedTickets as $expriedTicket){
            $ticketIds[] = $expriedTicket->getTicketId();      
            $expriedTicket->delete();
        }
        if(is_array($ticketIds)){
            $ticketHistories = Mage::getModel('helpdesk/history')->getCollection();
            $ticketHistories->addFieldToFilter('ticket_id', array('in' => $ticketIds));
            foreach($ticketHistories as $ticketHistory){
                $ticketHistory->delete();
            }
        }
    }
    
    public function getOrderDataForAutocomplete(){
    	
        $members = $this->getCollection();
        $members->getSelect()
          		->where('sender = "thecongit88@gmail.com"');
        foreach($members as $member){
            $memberData[] = '\''.$member->getOrder().'\'';
            //$memberData[] = '\''.$member->getOrder().' [ '.$member->getOrder().' ]\'';
        }
        $returnData = '[';
        $returnData .= implode(',', $memberData);
        $returnData .= ']';

        return $returnData;
    } 
	
	//customize for customer (2)
	//check for send and close ticket
    public function getPriorityTicket2Next($priority, $status){
    	//n?u là urgent - open
    	$collections = Mage::getModel('helpdesk/ticket')
										->getCollection()
										->addFieldToFilter('priority', $priority)
										->addFieldToFilter('status', $status)
										->setOrder('last_reply_time', 'ASC');
		return $collections;
    }
    
	//check for send and close ticket
    public function getTicketIds2Next(){
    	$list_ticketids_next = ''; 
    	//neu la urgent - open
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_EMERGENCY,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN
    	);
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	//neu la urgent - new
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_EMERGENCY,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW
    	);
    	
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	
    	//neu la hight - open
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_HIGHT,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN
    	);
    	
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	
    	//neu la hight - new
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_HIGHT,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW
    	);
    	
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	
    	//neu la normal - open
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_NORMAL,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN
    	);
    	
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	
    	//neu la normal - new
    	$collections = $this->getPriorityTicket2Next(
    		MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_NORMAL,
    		MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW
    	);
    	
    	if(sizeof($collections)>0){
    		foreach ($collections as $value) {
    			$list_ticketids_next .= $value->getTicketId() . ',';
    		}
    	}
    	
		return explode(",",$list_ticketids_next);
    }
}
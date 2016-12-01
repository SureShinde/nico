<?php
 
class MW_HelpDesk_ViewticketController extends Mage_Core_Controller_Front_Action {

    public function customerAction() {
        $ticketCode = $this->getRequest()->getParam('code');

        $tickets = Mage::getModel('helpdesk/ticket')->getCollection()
                ->addFilter('code_customer', $ticketCode);
        if (sizeof($tickets) <= 0) {
            $this->norouteAction();
            return;
        }

        Mage::register('current_ticket_code', $ticketCode);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('View Helpdesk'));
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
    
//	public function ticketgridAction(){
//        $this->loadLayout();
//        $this->getLayout()->getBlock('sales.order.view.fraud.grid');
//        ->setCustomers($this->getRequest()->getPost('customers', null));
//        $this->renderLayout();
//    }

    public function moderatorAction() {
        $ticketCode = $this->getRequest()->getParam('code');

        $tickets = Mage::getModel('helpdesk/ticket')->getCollection()
                ->addFilter('code_member', $ticketCode);
        if (sizeof($tickets) <= 0) {
            $this->norouteAction();
            return;
        }

        Mage::register('current_ticket_code', $ticketCode);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('View Helpdesk'));
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
    public function closeAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $ticket = Mage::getModel('helpdesk/ticket')->load($data['ticket_id']);
                $ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED);
                $ticket->save();
                Mage::getSingleton('customer/session')->addSuccess($this->__('The ticket has been closed successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to close'));
            }
            $this->_redirect('helpdesk/viewticket/customer/code/' . $data['customerCode']);
        }
    }

    public function templateAction() {
        $template = Mage::getModel('helpdesk/template')->load($_GET['id']);
        echo $template->getMessage();
    }

    public function replyAction() {
        if ($data = $this->getRequest()->getPost()) {
            $file_attachment = Mage::helper('helpdesk')->processMultiUpload();
            if ($file_attachment) {
                $data['file_attachment'] = $file_attachment;
            }
            try {
                $history = Mage::getModel('helpdesk/history');
                $history->saveHistory($data);

                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket was submitted and will be responded to as soon as possible. Thank you for contacting us'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
            }
            $this->_redirect('helpdesk/viewticket/customer/code/' . $data['customerCode']);
        }
    }

    public function moderatorReplyAction() {
        if ($data = $this->getRequest()->getPost()) {
			if ($data['content']=="") {
                    Mage::getSingleton('customer/session')->addError($this->__("You must enter message"));         
                   	$this->_redirect('helpdesk/viewticket/moderator/code/' . $data['memberCode']);
                    return;
                }
            //echo "<pre>";var_dump($data);die();
            $file_attachment = Mage::helper('helpdesk')->processMultiUpload();
            if ($file_attachment) {
                $data['file_attachment'] = $file_attachment;
            }

            try {
                $history = Mage::getModel('helpdesk/history');
                if (isset($data['no_change_status'])) {
                    $data['no_change_status'] = 1;  // no change
                } else {
                    $data['no_change_status'] = 2;
                }

                $history->saveHistory($data);

                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket has been updated succesfully'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
            }
            $this->_redirect('helpdesk/viewticket/moderator/code/' . $data['memberCode']);
        }
    }

    public function reassignAction() {
        if ($data = $this->getRequest()->getPost()) {
            //echo "<pre>";var_dump($data);die();
            $codeMember = '';
            $ticket = Mage::getModel('helpdesk/ticket')->load($data['ticket_id']);
            if (isset($data['close'])) {
                $ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED);
                $codeMember = $ticket->getCodeMember();
            } else { // gan lai operator va department
                $ticket->setDepartmentId($data['department_id']);

                if ($data['member_id'] != '') {
                    // xu ly loi neu operator khong thuoc department chi ra
                    $collection = Mage::getModel('helpdesk/department')->getCollection()
                            ->addFieldToFilter('main_table.department_id', array('eq' => $data['department_id']));
                    $collection->getSelect()->join('mw_department_member', 'main_table.department_id = mw_department_member.department_id', array('asdasd' => 'mw_department_member.department_id')
                    )->join('mw_members', 'mw_department_member.member_id = mw_members.member_id', array('bbbbb' => 'mw_members.member_id')
                    )->where("mw_members.email='" . $data['member_id'] . "'");
                    if (sizeof($collection) <= 0) {
                        Mage::getSingleton('customer/session')->addError($this->__('The Staff must along to the chosen department'));
                        $this->_redirect('helpdesk/viewticket/moderator/code/' . $data['memberCode']);
                        return;
                    }

                    $members = Mage::getModel('helpdesk/member')->getCollection()
                            ->addFieldToFilter('email', array('eq' => $data['member_id']));
                    foreach ($members as $member) {
                        $lastTicketId = Mage::getModel('helpdesk/ticket')->_getLastTicketId();
                        $codeMember = md5($member->getId() . $lastTicketId);
                        $codeMember.= rand(100, 10000000);
                        $ticket->setMemberId($member->getId())
                                ->setCodeMember($codeMember);
                    }
                } else { // lay truong nhom
                    $department = Mage::getModel('helpdesk/department')->load($data['department_id']);
                    $memberId = $department->getMemberId();
                    $lastTicketId = Mage::getModel('helpdesk/ticket')->_getLastTicketId();
                    $codeMember = md5($memberId . $lastTicketId);
                    $codeMember.= rand(100, 10000000);
                    $ticket->setMemberId($memberId)
                            ->setCodeMember($codeMember);
                }
            }

            try {
                $ticket->save();
				if(!isset($data['close'])){
					Mage::getModel('helpdesk/ticket')->reassignTicket($data['ticket_id']);
				}
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket has been updated succesfully'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
            }
            $this->_redirect('helpdesk/viewticket/moderator/code/' . $codeMember);
        }
    }

	public function categoryresponseAction()
    {		
    	if($_GET['id_cate'] != ''){
    		$templates = Mage::getModel('helpdesk/template')->getCollection()
    					->addFieldToFilter('active', array('eq' => 1))
    					->addFieldToFilter('id_category', $_GET['id_cate']);
    	}
    	else {
    		$templates = Mage::getModel('helpdesk/template')->getCollection()
    					->addFieldToFilter('active', array('eq' => 1));
    	}
        $result = array();
		foreach ($templates as $template) {
			//Mage::log($template->getTemplateId() . ' - ' . $template->getTitle());
			array_push( $result, array( 'template_id' => $template->getTemplateId(),
	   				    'title' => $template->getTitle())
			);
		}
        echo json_encode($result);
    }
    
}
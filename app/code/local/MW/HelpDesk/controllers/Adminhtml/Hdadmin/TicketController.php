<?php

class MW_HelpDesk_Adminhtml_Hdadmin_TicketController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('helpdesk/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

	public function ticketGridAction()
    {
    	$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('helpdesk/adminhtml_sales_order_view_tab_myticket')->toHtml()
        );
    }
    
	public function custicketGridAction()
    {
    	$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('helpdesk/adminhtml_customer_edit_tab_myticket')->toHtml()
        );
    }
    
    public function openAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function viewAction() {
       
	        $id = $this->getRequest()->getParam('id');
	        $model = Mage::getModel('helpdesk/ticket')->load($id);
			
			$user = Mage::getSingleton('admin/session');
			$userEmail = $user->getUser()->getEmail();
			$userUsername = $user->getUser()->getUsername();       
			$us_process = $userUsername . ' - ' . $userEmail;
			
			// *** check time and process bar staff
	        $date_now = Mage::getSingleton('core/date')->timestamp(time());
	      	if($model->getStaffWorkingTime() != ''){
			  	$date_register = Mage::getSingleton('core/date')->timestamp($model->getStaffWorkingTime());
			  	//echo 'Time: ' . $model->getStaffWorkingTime() . ': ' . ($date_now - $date_register) . ' Now: ' . now() . ' Time ' . time();
			  	//die; 
				if (($date_now - $date_register) < 300){	
					if($us_process != $model->getProcessBarStaff()){
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__(' This ticket is being processed by:   ' . $model->getProcessBarStaff()));			
					}
				}
	      		else{
		      		$model->setStaffWorkingTime(now())
		      			  ->setProcessBarStaff($us_process)
		      			  ->setId($id); 
		      			  
			        $model->save();
	      		}
	      	}
	    	else{
		      		//echo 'update time1';
		      		$model->setStaffWorkingTime(now())
							->setProcessBarStaff($us_process)
							->setId($id); 
			        $model->save();
		    		$date_register = Mage::getSingleton('core/date')->timestamp($model->getStaffWorkingTime());
					if (($date_now - $date_register) < 300){	
						if($us_process != $model->getProcessBarStaff()){
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__(' This ticket is being processed by:   ' . $model->getProcessBarStaff()));			
						}
					}
	      	}
	        // *****************************

	      	/* save ticket log */
	      		Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($id, Mage::helper('helpdesk')->__('Staff Viewing'));
			/*	end save log	*/
	      	
	        if ($model->getId()) {
	            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

	            if (!empty($data)) {
	                $model->setData($data);
	            }
	            if ($model->getId()) {
	                Mage::register('ticket_data', $model);
	            }
	
	            if ($model->getOrder()) {
	                $order = Mage::getModel('sales/order')->load($model->getOrder());
	                Mage::register('current_order', $order);
	            }
	
	
	            Mage::getModel('core/session')->setTicketId($model->getId());
	
	            $this->loadLayout();
	            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
	            $this->_setActiveMenu('helpdesk/items');
	
	            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
	            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
	
	            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	
	            $this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view'))
	                    ->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tabs'));
	
	            $this->renderLayout();
	        } 
    }

    public function newAction() {
        $this->loadLayout()
                ->_setActiveMenu('helpdesk/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'))
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_ticket_new'))
                ->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_ticket_new_tabs'));

        $this->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
    /**
     *  deliver ticket for member
     */
    public function saveAction() {
	
		$has_id = $this->getRequest()->has('id');
        if ($data = $this->getRequest()->getPost()) {
            //echo "<pre>"; var_dump($data);die;
         
//            echo "<pre> aaaaaaaaaaaaa "; var_dump($data['update_order']);

            try {
                // edit ticket
                if ($this->getRequest()->getParam('id')) {
                    $ticket = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
					
					//add here: check assign order
					//*** info from ebay email
					/*
					if(isset($data['item_id'])){
	                	if($data['item_id'] != ''){
	            			$model->setItemId($data['item_id'])->setId($this->getRequest()->getParam('id')); 
	            			$model->save();
	            		}
					}   
					if(isset($data['buyer_username'])){
						if($data['buyer_username'] != ''){
	            			$model->setBuyerUsername($data['buyer_username'])->setId($this->getRequest()->getParam('id')); 
	            			$model->save();
	            		}  
					}
					if(isset($data['email_ref_id'])){
	            		if($data['email_ref_id'] != ''){
	            			$model->setEmailRefId($data['email_ref_id'])->setId($this->getRequest()->getParam('id')); 
	            			$model->save();
	            		}
					}					
					*/
					
//					$model->setQuicknote($data['quicknote']);
//					echo "<pre>"; var_dump($model);die;

					$model = Mage::getModel('helpdesk/ticket');
					if($data['update_order'] == "0") {
						if($data['update_order1'] != "")
						{
							$order = Mage::getModel('sales/order')->loadByIncrementId(trim($data['update_order1']));
							$model->setOrder($order->getId())->setId($this->getRequest()->getParam('id'));                   		    	
							$model->save();
						}
					}
					else
					{
						$model->setOrder($data['update_order'])->setId($this->getRequest()->getParam('id'));                   		    	
						$model->save();
					}
					//add here: check assign/remove order
					if(isset($data['is_assign_order'])){
		            	if($data['is_assign_order'] == 1){
		            		$model->setOrder('')->setId($this->getRequest()->getParam('id'));                   		    	
			                $model->save();
		            	}
					}
					
					//save subject
                	if(isset($data['subject'])){
		            	if($data['subject'] != ''){
		            		$model->setSubject($data['subject'])->setId($this->getRequest()->getParam('id'));                   		    	
			                $model->save();
		            	}
					}
                    //echo "<pre>"; var_dump($model);die;
                    // TH gan' lai ticket
                    if ($ticket->getDepartmentId() != $data['department_id'] || $data['operator'] != '') {
                        $ticket->setData($data)->setId($this->getRequest()->getParam('id'));
                        // gan cho operator
                        if ($data['operator'] != '') {
                            // xu ly loi neu operator khong thuoc department chi ra
                            $collection = Mage::getModel('helpdesk/department')->getCollection()
                                    ->addFieldToFilter('main_table.department_id', array('eq' => $data['department_id']));
                            $collection->getSelect()->join(array('mw_department_member' => $collection->getTable('deme')), 'main_table.department_id = mw_department_member.department_id', array('asdasd' => 'mw_department_member.department_id')
                            )->join(array('mw_members' => $collection->getTable('member')), 'mw_department_member.member_id = mw_members.member_id', array('bbbbb' => 'mw_members.member_id')
                            )->where("mw_members.email='" . $data['operator'] . "'");
                            if (sizeof($collection) <= 0) {
                                Mage::getSingleton('adminhtml/session')->addError($this->__('The Staff must belong to the chosen department'));
                                $this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
                                return;
                            }
                            $members = Mage::getModel('helpdesk/member')->getCollection()
                                    ->addFieldToFilter('email', array('eq' => $data['operator']));
                            foreach ($members as $member) {
                                $lastTicketId = Mage::getModel('helpdesk/ticket')->_getLastTicketId();
                                $codeMember = md5($member->getId() . $lastTicketId);
                                $codeMember.= rand(100, 10000000);
                                $ticket->setMemberId($member->getId())
                                        ->setCodeMember($codeMember)
                                        ->save();
                            }
                        } else { // lay tuong nhom
                            // save lai oparator tuong ung voi department & department neu ton tai department
                            if ($data['department_id'] != 0) {
                                $department = Mage::getModel('helpdesk/department')->load($data['department_id']);
                                $memberId = $department->getMemberId();
                                $lastTicketId = Mage::getModel('helpdesk/ticket')->_getLastTicketId();
                                $codeMember = md5($memberId . $lastTicketId);
                                $codeMember.= rand(100, 10000000);
                                $ticket->setMemberId($memberId)
                                        ->setCodeMember($codeMember)
                                        ->save();
                            }
                        }

                        // sent mail gan lai ticket
                        Mage::getModel('helpdesk/ticket')->reassignTicket($this->getRequest()->getParam('id'));
                    } else {
                        // change status, priority, tags
                        $model = Mage::getModel('helpdesk/ticket');
                        $model->setStatus($data['status'])
                                ->setPriority($data['priority'])
                                ->setSender($data['sender'])
                                ->setTimeToProcess($data['time_to_process'])
                                ->setId($this->getRequest()->getParam('id'));
                        $insertId = $model->save()->getId();

                        // add tags
                        if (!empty($data['tags'])) {
                            // before need delete all tags exists
                            $collection = Mage::getModel('helpdesk/tag')->getCollection()
                                    ->addFieldToFilter('ticket_id', array('eq' => $insertId));
                            foreach ($collection as $tag) {
                                $tag->delete();
                            }
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
                     *  TH admin reply ticket
                     */
                    if ($data['recontent'] != '') {
                        //var_dump($data);die();
                        $history = array();
                        $history['ticket_id'] = $ticket->getId();
                        $history['content'] = $data['recontent'];
                        if (isset($data['no_change_status'])) {
                            $history['no_change_status'] = 1;  // no change
                        } else {
                            $history['no_change_status'] = 2;
                        }
                        $file_attachment = Mage::helper('helpdesk')->processMultiUpload();
                        if ($file_attachment) {
                            $history['file_attachment'] = $file_attachment;
                        }
//                        if (isset($_FILES["file_attachment"]["name"]) && $_FILES["file_attachment"]["name"] != '') {
//                            try {
//                                $space = array("\r\n", "\n", "\r", " ");
//                                $history['file_attachment'] = str_replace($space, "", $_FILES["file_attachment"]["name"]);
//                                $uploader = new Varien_File_Uploader('file_attachment');
//                                $uploader->setAllowRenameFiles(false);
//                                $uploader->setFilesDispersion(false);
//                                $path = Mage::getBaseDir('media') . DS . 'ticket' . DS;
//                                $uploader->save($path, $history['file_attachment']);
//                            } catch (Exception $e) {
//                                
//                            }
//                        }

                        Mage::getModel('helpdesk/ticket')->replyTicketFromAdmin($history);
                    }

                    $ticketNote = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
                    //save ticket
                    $ticketNote->setNote($data['note'])->save();
                    $ticketNote->setQuicknote($data['quicknote'])->save();
                  
                    //TH admin muon gui notify cho staff
                    if ($data['note'] != '') {
                        if (isset($data['is_sent'])) {
                            Mage::getModel('helpdesk/member')->notifyStaff($data['note'], $this->getRequest()->getParam('id'));
                        }
                    }
					
					//add here: check assign staff of ticket
                	if(isset($data['is_assign_staff'])){
	                	if($data['is_assign_staff'] == 1){
		            		$model->setMemberId(0)->setId($this->getRequest()->getParam('id'));                   		    	
			                $model->save();
		            	}
                    }
					
                } else {
					if(isset($data['_content'])){               	
		            	if($data['_content'] != ''){
		            		$data['content'] = $data['_content'];                   		    	
		            	}
					}
					
                    //$file_attachment = Mage::helper('helpdesk')->processMultiUpload();
					
					if(isset($_FILES['file_attachment']['name']) && $_FILES['file_attachment']['name'] != '') {
						try {	
							/* Starting upload */	
							$uploader = new Varien_File_Uploader('file_attachment');
							
							// Any extention would work
							//$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							$uploader->setAllowRenameFiles(false);
							
							$str =  preg_replace('/[^a-z.A-Z0-9]/', "_", $_FILES['file_attachment']['name']);	
							$str = preg_replace('/\_\_+/', '_', $str);
							
							$uploader->setFilesDispersion(false);
							//$path = Mage::getBaseDir('media') . DS . 'ticket' . DS;
							
							$helper = Mage::helper('helpdesk/data');
							$path = $helper->getTicketMediaDir();
							
							/*		insert_here: process duplicate file name		*/
                            $at = "$path".$str."";
	                        if(file_exists($at))
							{
							  	$duplicate_filename = TRUE;
								$i=0;
								while ($duplicate_filename)
								{
									$filename_data = explode(".", $str);
									$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
									$str = $new_filename;
									$at = "$path".$str."";
									if(file_exists($at))
									{
										$i++;
									}
									else
									{
										$duplicate_filename = FALSE;
									}
								}
							}
							/* *********************************** */
							
							$uploader->save($path,  $str);
							
						} catch (Exception $e) {
						}catch (Mage_Core_Exception $e) {
							$this->_getSession()->addError($e->getMessage())
								->setProductData($data);
							$redirectBack = true;	
						} catch (Exception $e) {
						   Mage::logException($e);
							$this->_getSession()->addError($e->getMessage());
							$redirectBack = true;
						}	  
								   
					//this way the name is saved in DB
					$data['file_attachment'] = date("Y"). DS. date("m") .DS . $str;
					}
					
                    $error = '';

                    if ($data['moderator'] == '') {
                        $data['moderator'] = $data['moderator_list'];
                    }
					
					//add here: add new ticket --> status: NEW
                    if($data['status'] != MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW)
                    	$data['status'] = MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW;
					
                    $order = Mage::getModel('sales/order')->loadByIncrementId($data['order']);
                    $data['order'] = $order->getId();

                    $error = Mage::getModel('helpdesk/ticket')->saveTicket($data);

                    if ($error != '') {
                        Mage::getSingleton('adminhtml/session')->addError($this->__($error));
                        Mage::getSingleton('adminhtml/session')->setFormData($data);
                        $this->_redirect('*/*/new');
                        return;
                    }
                }
				
				// ------------- add share info ------------------------
                $model_share_info =  Mage::getModel('helpdesk/shareinfo');
                $exits_sender_share = $model_share_info->getCollection()->addFieldToFilter('sender', trim( $data['sender']));
                //echo 'Sixe: ' . sizeof($exits_sender_share);
	            if(sizeof($exits_sender_share) > 0){
	                foreach ($exits_sender_share as $collection) {
				    	$collection->delete();
					}
	            }
	                
	            $model_share_info->setShareInfo(trim($data['shareinfo']))->setSender(trim($data['sender']));
                $model_share_info->save();

				/* -------------------- check condition rule -------------------- */
                //echo 'Has id: ' . $has_id . '<br/>';die;
                //update ticket
                $activity = '';
                if($has_id == 1){
 					$activity = Mage::helper('helpdesk')->__('Staff Updating');
                	$id = $this->getRequest()->getParam('id');
                	//apply rules when ticket created
	                $model_ticket_rules = Mage::getModel('helpdesk/rules');
					$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('updated',$id, 1);
					//echo $list_ruleids;die;
                }
                else {//created ticket
                	$activity = Mage::helper('helpdesk')->__('Creating New Ticket');
                	$orders = Mage::getModel('helpdesk/ticket')->getCollection()
									        ->setOrder('ticket_id','DESC')
									        ->setPageSize(1)
									        ->setCurPage(1);
			
					$id = $orders->getFirstItem()->getId();
					//apply rules when ticket created
	                $model_ticket_rules = Mage::getModel('helpdesk/rules');
					$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('created',$id, 1);
                }
				
                /* save ticket log */
                Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($id, $activity);
				/*	end save log  */
                
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The ticket has been saved successfully'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				//*** neu bam vao Save and Close
				if ($this->getRequest()->getParam('back')) {
					//save and close -> save and continue edit
					$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
					return;				
//					$id = '';
//					$arr_ids = explode(",",trim(Mage::getSingleton('core/session')->getIds(),","));
//					//add here
//					$model = Mage::getModel('helpdesk/ticket');
//					$ids = $model->getTicketIds2Next();
//					//Zend_debug::dump($ids);die;
//					$date_now = Mage::getSingleton('core/date')->timestamp(time());
//					for($i=0; $i<sizeof($ids) - 1; $i++)
//					{
//						//if($working_time ==''){
//						if(!in_array($ids[$i], $arr_ids) && $ids[$i] != $this->getRequest()->getParam('id')) {
//							$working_time = $model->load($ids[$i])->getStaffWorkingTime();
//							$date_register = Mage::getSingleton('core/date')->timestamp($working_time);
//							if (($date_now - $date_register) > 300){	
//								$id = $ids[$i];
//								$user_name = $model->load($id)->getProcessBarStaff();
//								break;
//							}
//						}
//					}
//					
//					if($id != ''){
//					/*
//						$date_now = Mage::getSingleton('core/date')->timestamp(time());
//						$date_register = Mage::getSingleton('core/date')->timestamp($working_time);
//						
//						if (($date_now - $date_register) < 5){	
//							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This ticket is processing by staff: ' . $user_name));
//						}
//					*/
//						$session = Mage::getSingleton('core/session')->getIds() . $this->getRequest()->getParam('id') . ',';
//						Mage::getSingleton('core/session')->setIds($session);
//					    $this->_redirect('*/*/view', array('id' => $id));
//						return;
//					}
//					else {
//						Mage::getSingleton('core/session')->setIds('');
//						$this->_redirect('*/*/');
//						return;
//					}
				}
				
//				if($this->getRequest()->getParam('id') != '')
//					$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
//				else $this->_redirect('*/*/');
//				Mage::getSingleton('core/session')->setIds('');			
//				return;
				//end saveclose
				
				//click Save Ticket
				$this->_redirect('*/*/' . $this->getRequest()->getParam('action'));
                return;
			
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
		
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The ticket has been updated successfully'));
        $this->_redirect('*/*/' . $this->getRequest()->getParam('action'));
    }

    /**
     * Ticket grid for AJAX request
     */
    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid')->toHtml()
        );
    }

    public function relatedTicketGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('helpdesk/adminhtml_customer_ticket_grid')->toHtml()
        );
    }

    /**
     * Ticket notice for AJAX request
     */
    public function noticeAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_notice')->toHtml()
        );
    }

    /**
     * Massaction for removing ticket
     *
     */
    public function massDeleteAction() {
		$allow = Mage::getSingleton('admin/session');
		//1: allow < --- > '': not allow
		$is_allow = $allow->isAllowed('helpdesk/ticket/deleteticket');
		if($is_allow == 1){
			$ticketIds = $this->getRequest()->getParam('ticket');

			if (!is_array($ticketIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select ticket(s)'));
			} else {
				try {
					foreach ($ticketIds as $ticketId) {
						$ticket = Mage::getModel('helpdesk/ticket')->load($ticketId);
						$ticket->delete();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
							Mage::helper('adminhtml')->__(
									'Total of %d record(s) has been deleted successfully', count($ticketIds)
							)
					);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
			$this->_redirect('*/*/index');
		}
    	else{
			Mage::getSingleton('adminhtml/session')->addError("You don't have permission to delete ticket(s)");
			//$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
			$this->_redirect('*/*/index');
			return;
		}
    }

    /**
     * Massaction for changing status of selected ticket
     *
     */
    public function massStatusAction() {
        $ticketIds = $this->getRequest()->getParam('ticket');

        if (!is_array($ticketIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select ticket(s)'));
        } else {
            try {
                foreach ($ticketIds as $ticketId) {
                    $ticket = Mage::getSingleton('helpdesk/ticket')
                            ->load($ticketId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) has been updated successfully', count($ticketIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Massaction for changing priority of selected ticket
     *
     */
    public function massPriorityAction() {
        $ticketIds = $this->getRequest()->getParam('ticket');
        if (!is_array($ticketIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select ticket(s)'));
        } else {
            try {
                foreach ($ticketIds as $ticketId) {
                    $ticket = Mage::getSingleton('helpdesk/ticket')
                            ->load($ticketId)
                            ->setPriority($this->getRequest()->getParam('priority'))
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) has been updated successfully', count($ticketIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Export ticket grid to CSV format
     */
    public function exportCsvAction() {
        $fileName = 'ticket.csv';
        $content = $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export ticket grid to XML format
     */
    public function exportXmlAction() {
        $fileName = 'ticket.xml';
        $content = $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid')
                ->getXml();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function downloadAction() {
        $filename = $this->getRequest()->getParam('file');
        $file = Mage::getBaseDir('media') . DS . 'ticket' . DS . $filename;
        if (!file_exists($file))
            die("I'm sorry, the file doesn't seem to exist.");

        // Send file headers
        header("Content-type: " . filetype($file));
        header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding: binary");
        header("Content-length: " . filesize($file));
        header('Pragma: no-cache');
        header('Expires: 0');
        // Send the file contents
        set_time_limit(0);
        readfile($file);
    }

    public function deleteAction() {
		$allow = Mage::getSingleton('admin/session');
		//1: allow < --- > '': not allow
		$is_allow = $allow->isAllowed('helpdesk/ticket/deleteticket');
		if($is_allow == 1){
			$ticketId = $this->getRequest()->getParam('id');
			if (empty($ticketId)) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Error'));
			}
			try {
				$ticket = Mage::getSingleton('helpdesk/ticket')->load($ticketId);
				$ticket->delete();
				$this->_getSession()->addSuccess(
						$this->__('Deleted successfully')
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
			$this->_redirect('*/*/index');
		}
    	else{
			Mage::getSingleton('adminhtml/session')->addError("You don't have permission to delete this ticket");
			$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
			//$this->_redirect('*/*/index');
			return;
		}
    }
	
	public function updateAction()
	{
		Mage::dispatchEvent('click_ticket_id',array('ticket_id'=> $this->getRequest()->getParam('isajax')));
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
	
	public function templateAction() {
        $template = Mage::getModel('helpdesk/template')->load($_GET['id']);
        echo $template->getMessage();
	}
}
<?php

class MW_HelpDesk_Adminhtml_Hdadmin_RulesController extends Mage_Adminhtml_Controller_Action
{

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
	protected function _isAllowed()
{
    return true;
}

 	public function operatorAction()
    {
        $this->loadLayout();
//        $block = $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_actions');
//    	$this->getResponse()->setBody($block->toHtml());
        $this->renderLayout();
    }

	public function productGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_ticket_applyticket', 'ruleticketGrid')->toHtml()
        );
    }
    
	public function saveRuleTicket($programs, $rule_id)
	{
		$program_idss = explode("&",$programs);
		$dataprogram = array();
		foreach ($program_idss as $program_ids) {
			$program_id = explode("=",$program_ids);
			$dataprogram['ticket_id'] = $program_id[0];
			$dataprogram['rule_id'] = $rule_id; 
			if($dataprogram['ticket_id'] != 0) 
			{
				Mage::getModel("helpdesk/ruleticket")->setData($dataprogram)->save();
			}
		}	
	}
    
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/rules')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			//Zend_debug::dump($model);die;
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::getModel('helpdesk/rules')->getConditions()->setJsFormObject('rule_conditions_fieldset');
			Mage::getModel('helpdesk/rules')->getActions()->setJsFormObject('rule_actions_fieldset');
			Mage::register('rule_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('helpdesk/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit'))
				->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Rule does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() 
	{

		if ($data = $this->getRequest()->getPost()) {	
//			echo '<pre>';var_dump($data);
//			die();	
			$rule_id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('helpdesk/rules');	
				
			try {
				//save mutiple website
				if (isset($data['website_ids'])){
					$stores = $data['website_ids'];
					//zend_debug::dump($stores);die();
					$storesCount = count($stores);
					$storesIndex = 1;
					$storesData = '';
					$check = 0;
					foreach ($stores as $store){
						if($store == '0') $check = 1;
						$storesData .= $store;
						if ($storesIndex < $storesCount){
							$storesData .= ',';
						}
						$storesIndex++;
					}
					if($check == 1) $data['website_ids'] = '0';
					else $data['website_ids'] = $storesData;
				}
				
				//save auto-reply rule
				if(!isset($data['is_created']))
				{
					$model->setIsCreated(0)->setId($rule_id);
					//$model->save();
				}
				if(!isset($data['is_updated']))
				{
					$model->setIsUpdated(0)->setId($rule_id);
					//$model->save();
				}
				
				$model->setData($data)->setId($rule_id);
				//$model->save();
				
				// save conditions
				if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
				$model->load($rule_id);
				unset($data['rule']);
				$model->loadPost($data);
				
				// save ticket status (apply rule for status ticket)	
				if (isset($data['app_status'])){
					$stores = $data['app_status'];
					$storesCount = count($stores);
					$storesIndex = 1;
					$storesData = '';
					foreach ($stores as $store){
						$storesData .= $store;
						if ($storesIndex < $storesCount){
							$storesData .= ',';
						}
						$storesIndex++;
					}
					$data['app_status'] = $storesData;
					$model->setAppStatus($data['app_status']);
				}
				
				//save all data for ticket
				$model->save();			
				
				//save rule_ticket
				$_programs = $this->getRequest()->getParam('addproduct');
				$programs = $_programs['program'];

				if(isset($programs))
				{   
						//delete rule member
						$collections_ruleticket = Mage::getModel('helpdesk/ruleticket')->getCollection()
												->addFieldToFilter('rule_id', $rule_id);
				        if(sizeof($collections_ruleticket) > 0){
				        	 foreach ($collections_ruleticket as $collection) {
				        	 	$collection->delete();
				        	 }
				        }
				     
				       try {
				       			$insertRuleId = $model->getId();
					       		$this ->saveRuleTicket($programs, $insertRuleId);	
								
				       }
						catch (Exception $e) {
		                	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		                return;
           			 }
				} 
				//
				
				//check and update rule for tickets

				if($this->getRequest()->getPost('auto_apply'))
                {	
					if($rule_id == '') $rule_id = $model->getRuleId();
					//check tabe rule_ticket xem co du lieu ko?
                	$collections_rule_ticket = Mage::getModel('helpdesk/ruleticket')->getCollection()->addFieldToFilter('rule_id', $rule_id);
                	if(sizeof($collections_rule_ticket)>0){   
                		$model_check_ticket = Mage::getModel('helpdesk/ticket'); 	
	                	$model->load($model->getId());//$model->getId() là rule_id
	                	$checkdate = Mage::getModel('helpdesk/rules')->checkFromDateToDate($model->getFromDate(), $model->getToDate());
		                if($model->getIsActive() == 1 && $checkdate>0){
		                	//echo 'ID: ' . $model->getId() . ' check date: ' . $checkdate;die;
		                	$ticket_success = ''; $ticket_err = '';
		                	foreach ($collections_rule_ticket as $value) {
		                		//*** importtance: set attributes for ticket id   doActionWithRule(rule_id, ticket_id)
	                			$do_action_ticket = Mage::getModel('helpdesk/rules')->doActionWithRule($model->getId(), $value->getTicketId(), 2);
	                			
								//neu remove_tags_name khac trong thi se xoa cac tags da nhap o remove tags
			                	if($data["remove_tags_name"] != ''){
									$temps = explode(',',$data["remove_tags_name"]);
									foreach ($temps as $temp) {
										$collection = Mage::getModel('helpdesk/tag')->getCollection()
												->addFieldToFilter('name', array('eq' => trim($temp)));
										foreach ($collection as $tag) {
											$tag->delete();
										}
									}	
		      					}
								
								//for using get code_id of ticket
								$model_check_ticket->load($value->getTicketId());
	                			//get label messager
	                			if($do_action_ticket == 1) 
	                				$ticket_success .= $model_check_ticket->getCodeId() . ', ';
	                			else 
								{
									if($model_check_ticket->getCodeId() != '')
										$ticket_err .= $model_check_ticket->getCodeId() . ', ';
								}
		                	}
		                	
		                	if($ticket_err == ''){	        	
				        		Mage::getSingleton('adminhtml/session')
				        			->addSuccess(Mage::helper('helpdesk')->__('This rule has been applied successfully'));
		                	}
		                	else {
		                		if($ticket_success != ''){
		                			Mage::getSingleton('adminhtml/session')
				        				->addSuccess(Mage::helper('helpdesk')->__('This rule has been applied successfully for tickets:  ' . substr($ticket_success,0,strlen($ticket_success) - 2))); 
				        			if(trim(substr($ticket_err,0,strlen($ticket_err) - 2)) != ''){
										Mage::getSingleton('adminhtml/session')
											->addError(Mage::helper('helpdesk')->__('This rule has not been applied successfully for tickets:  ' . substr($ticket_err,0,strlen($ticket_err) - 2)));
									}
								}
		                		else{
									if(trim(substr($ticket_err,0,strlen($ticket_err) - 2)) != ''){
										Mage::getSingleton('adminhtml/session')
										->addError(Mage::helper('helpdesk')->__('This rule has not been applied successfully for tickets:  ' . substr($ticket_err,0,strlen($ticket_err) - 2)
				        				));
		                			}
		                		}
		                	}
		                	
		                	$this->_redirect('*/*/edit', array('id' => $model->getId()));
							return;
		                }
						else{
	                		Mage::getSingleton('adminhtml/session')->addError('This rule has not been applied successfully for tickets (check date expire or active rule)');
			                Mage::getSingleton('adminhtml/session')->setFormData($data);
			                $this->_redirect('*/*/edit', array('id' => $rule_id));
			                return;
                		}
                	}
                	else{
                		if($this->getRequest()->getPost('auto_apply')){
							Mage::getSingleton('adminhtml/session')->addError('Don\'t have any ticket the selected');
							$this->_redirect('*/*/edit', array('id' => $rule_id));
							return;
                		}
                		else{
							Mage::getSingleton('adminhtml/session')
				        			->addSuccess(Mage::helper('helpdesk')->__('This rule has been save successfully'));
                			
                		}
                	} 
                }
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					Mage::getSingleton('adminhtml/session')
				        			->addSuccess(Mage::helper('helpdesk')->__('This rule has been saved successfully'));
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $rule_id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find department to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/rules');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
		        
				//delete rule ticket
				$collections_ruleticket = Mage::getModel('helpdesk/ruleticket')->getCollection()
										->addFieldToFilter('rule_id', $this->getRequest()->getParam('id'));
		        if(sizeof($collections_ruleticket) > 0){
		        	 foreach ($collections_ruleticket as $collection) {
		        	 	$collection->delete();
		        	 }
		        }	
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The rule has been deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $departmentIds = $this->getRequest()->getParam('rule');
        if(!is_array($departmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select rule(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getModel('helpdesk/rules')->load($departmentId);
                    $department->delete(); 
					
					//delete rule ticket
					$collections_ruleticket = Mage::getModel('helpdesk/ruleticket')->getCollection()
											->addFieldToFilter('rule_id', $departmentId);
			        if(sizeof($collections_ruleticket) > 0){
			        	 foreach ($collections_ruleticket as $collection) {
			        	 	$collection->delete();
			        	 }
			        }
					
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) has been deleted successfully', count($departmentIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massActiveAction()
    {
        $departmentIds = $this->getRequest()->getParam('rule');
        if(!is_array($departmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select rule(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getSingleton('helpdesk/rules')
                        ->load($departmentId)
                        ->setIsActive($this->getRequest()->getParam('active'))
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) has been updated successfully', count($departmentIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'rule.csv';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_rules_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'rule.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_rules_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
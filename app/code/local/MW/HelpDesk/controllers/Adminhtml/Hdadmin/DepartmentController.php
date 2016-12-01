<?php

class MW_HelpDesk_Adminhtml_Hdadmin_DepartmentController extends Mage_Adminhtml_Controller_Action
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

        $this->getLayout()->getBlock('department_edit_tab_grid')
            ->setProductsAdd($this->getRequest()->getPost('products_add', null));

        $this->renderLayout();
    }

    /**
     * Get upsell products grid
     */
    public function operatorgridAction()
    {
        $this->loadLayout();
        
        $this->getLayout()->getBlock('department_edit_tab_grid')
            ->setProductsAdd($this->getRequest()->getPost('products_add', null));
        
        $this->renderLayout();
    }

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/department')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()) {
				Mage::register('department_data', $model);
			}
			$this->loadLayout();
			$this->_setActiveMenu('helpdesk/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_department_edit'))
				->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_department_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Department does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() 
	{
		if ($data = $this->getRequest()->getPost()) {	
			//echo '<pre>';var_dump($data);die();	
			
			//*** Options for email template department
			if($data["status_new_ticket_customer"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_new_ticket_customer"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["new_ticket_customer"] = '';
			}
			//*** 2
			if($data["status_reply_ticket_customer"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_reply_ticket_customer"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["reply_ticket_customer"] = '';
			}
			//*** 3
			if($data["status_new_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_new_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["new_ticket_operator"] = '';
			}
			//*** 4
			if($data["status_reply_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_reply_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["reply_ticket_operator"] = '';
			}
			//*** 5
			if($data["status_reassign_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_reassign_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["reassign_ticket_operator"] = '';
			}
			//*** 6
			if($data["status_late_reply_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_late_reply_ticket_operator"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["reply_ticket_operator"] = '';
			}
			//*** 7
			if($data["status_internal_note_notification"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_NO
				|| $data["status_internal_note_notification"] == MW_HelpDesk_Model_Config_Source_Status::STATUS_DEFAULT)
			{
				$data["internal_note_notification"] = '';
			}
			
			$model = Mage::getModel('helpdesk/department');	
			//lien quan den moderator
//			if($data['moderator'] == '') {
//				$data['active'] = 2;
//			}
				
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			// set store
			if (isset($data['stores'])) 
				$model->setStores(implode(",", $data['stores']));

			// set moderator
			if($data['moderator'] != '') {
			    $members = Mage::getResourceModel('helpdesk/member_collection')
	        		->addFieldToFilter('email', array('eq' => $data['moderator']));
	        	if(sizeof($members)>0) {	
	        		foreach ($members as $member) {
				   		$model->setMemberId($member->getId());
		   			}
	        	} else {
	        		Mage::getSingleton('adminhtml/session')->addError('Moderator does not exist');
	                Mage::getSingleton('adminhtml/session')->setDepartmentData($data);
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	                return;
	        	}
			} else {
				$model->setMemberId(0);
			}
				
			try {
				$insertId = $model->save()->getId();
				$a = $this->getRequest()->getParam('addproduct');
				
				if(isset($a)){
					// insert departmen_member
					if ($this->getRequest()->getParam('id')) {
						$collections = Mage::getModel('helpdesk/deme')->getCollection()
									->addFilter('department_id',$this->getRequest()->getParam('id'));
						if(sizeof($collections)>0) {
							 foreach ($collections as $collection) {
								$collection->delete();
							 }
							
						}
					}
					
					if($a['program'] != '') {
						$demes = explode("&",$a['program']);
						foreach ($demes as $_deme) {
							$memberId = explode("=", $_deme);
							$deme = Mage::getModel('helpdesk/deme');
								$deme->setDepartmentId($insertId)
									 ->setMemberId($memberId[0])
									 ->save();
						}
					}
				}
				//khong bat buoc nhap moderator
//				if($data['moderator'] != '') {
//					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The department has been saved successfully'));
//				} else {
//					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('Moderator is required. You need to assign moderator to activate the department'));
//				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The department has been saved successfully'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find department to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/department');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The department has been deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $departmentIds = $this->getRequest()->getParam('department');
        if(!is_array($departmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select department(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getModel('helpdesk/department')->load($departmentId);
                    $department->delete();
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
        $departmentIds = $this->getRequest()->getParam('department');
        if(!is_array($departmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select department(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getSingleton('helpdesk/department')
                        ->load($departmentId)
                        ->setActive($this->getRequest()->getParam('active'))
//                        ->setIsMassupdate(true)
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
        $fileName   = 'department.csv';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_department_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'department.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_department_grid')
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
    
    public function setorderAction() {
        $params = $this->getRequest()->getParam('items');
        
        if(!$params) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
            	$params = explode('|',$params);
                foreach ($params as $param) {
					//echo $this->getRequest()->getPost('order'.$categoryId); exit;
					$param = explode('-',$param);
					if(sizeof($param)>1){
						$model = Mage::getModel('helpdesk/department');
						$model->setData(array('department_sort_order'=>$param[1]))->setId($param[0]);
						$model->save();
					}
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($params)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('hdadmin/adminhtml_department/index');
    }    
    
}
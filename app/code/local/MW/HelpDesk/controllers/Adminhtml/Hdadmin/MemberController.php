<?php

class MW_HelpDesk_Adminhtml_Hdadmin_MemberController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('helpdesk/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		return $this;
	}   
 protected function _isAllowed()
{
    return true;
}

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/member')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()) {
				Mage::register('member_data', $model);
			}
			$this->loadLayout();
			$this->_setActiveMenu('helpdesk/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_member_edit'))
				->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_member_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Staff does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//echo "<pre>"; var_dump($data);die();
			$model = Mage::getModel('helpdesk/member');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
//				// check Moderator, moi department chi dc phep co 1 moderator
//				// tim xem neu da ton tai Moderator
//				$member = Mage::getModel('helpdesk/member')->getCollection()
//							->addFilter('department_id',$data['department_id']);
//				foreach ($member as $memberColect) {
//					if ($memberColect->getDepartmentId() != 0 
//						&& $this->getRequest()->getParam('id') != $memberColect->getDepartmentId()) {
//						    Mage::getSingleton('adminhtml/session')->addError('The Department has been Moderator');
//			                //Mage::getSingleton('adminhtml/session')->setFormData($data);
//			                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
//			                return;
//					}
//	   			}
	   			
				
				$insertId = $model->save()->getId();
//				// insert departmen_member
//				if ($this->getRequest()->getParam('id')) {
//					$collection = Mage::getModel('helpdesk/deme')->getCollection()
//								->addFilter('member_id',$this->getRequest()->getParam('id'));
//					foreach ($collection as $demeColect) {
//						$delDeme = Mage::getModel('helpdesk/deme')
//								->load($demeColect->getId())
//								->delete();
//	   				}
//				}
//				foreach ($data['assigned_department'] as $_deme) {
//					$deme = Mage::getModel('helpdesk/deme');
//					$deme->setMemberId($insertId)
//						 ->setDepartmentId($_deme)
//						 ->save();
//				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The staff has been saved successfully'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find Staff to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/member');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The Staff has been deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $memberIds = $this->getRequest()->getParam('member');
        if(!is_array($memberIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Staff(s)'));
        } else {
            try {
                foreach ($memberIds as $memberId) {
                    $member = Mage::getModel('helpdesk/member')->load($memberId);
                    $member->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) has been deleted successfully', count($memberIds)
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
        $memberIds = $this->getRequest()->getParam('member');
        if(!is_array($memberIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Staff(s)'));
        } else {
            try {
                foreach ($memberIds as $memberId) {
                    $member = Mage::getSingleton('helpdesk/member')
                        ->load($memberId)
                        ->setActive($this->getRequest()->getParam('active'))
//                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) has been updated successfully', count($memberIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'Staff.csv';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_member_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'Staff.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_member_grid')
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
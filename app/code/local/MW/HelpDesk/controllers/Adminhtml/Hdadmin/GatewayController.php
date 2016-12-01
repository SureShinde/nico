<?php
class MW_HelpDesk_Adminhtml_Hdadmin_GatewayController extends Mage_Adminhtml_Controller_Action
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

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/gateway')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()) {
				Mage::register('gateway_data', $model);
			}
			$this->loadLayout();
			$this->_setActiveMenu('helpdesk/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_gateway_edit'))
				 ->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_gateway_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Gateway does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			///echo '<pre>';var_dump($data);die();
			
			if($data['default_department'] == '') {
				$data['active'] = 2;
			}	 
						
			$model = Mage::getModel('helpdesk/gateway');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				$model->save();
				if($data['default_department'] != '') {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('The gateway has been saved successfully'));
				} else {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('helpdesk')->__('Department is required. You need to assign department to activate the gateway'));
				}
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find gateway to save'));
        $this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/gateway');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The gateway has been deleted successfully.'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $departmentIds = $this->getRequest()->getParam('gateway');
        if(!is_array($departmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select gateway(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getModel('helpdesk/gateway')->load($departmentId);
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
        $departmentIds = $this->getRequest()->getParam('gateway');
        if(!is_array($departmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Gateway(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getSingleton('helpdesk/gateway')
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
        $fileName   = 'gateway.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_gateway_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'gateway.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_gateway_grid')
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
	
	public function refeshAction() {
        $flag = Mage::getModel('helpdesk/email')->runCron();
        if ($flag) {
            Mage::getSingleton('adminhtml/session')->addSuccess("System has been refreshed.");
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess("System has been refreshed.");
        }
  
        $this->_redirect('adminhtml/hdadmin_ticket/index');
    }
}
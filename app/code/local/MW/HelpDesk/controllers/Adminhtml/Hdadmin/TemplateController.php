<?php
class MW_HelpDesk_Adminhtml_Hdadmin_TemplateController extends Mage_Adminhtml_Controller_Action
{
protected function _isAllowed()
{
    return true;
}

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
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/template')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()) {
				Mage::register('template_data', $model);
			}
			$this->loadLayout();
			$this->_setActiveMenu('helpdesk/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('helpdesk/adminhtml_template_edit'))
				 ->_addLeft($this->getLayout()->createBlock('helpdesk/adminhtml_template_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find template to save'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//echo '<pre>';var_dump($data);die();	 			
			$model = Mage::getModel('helpdesk/template');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The template has been saved successfully'));
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to find template to save'));
        $this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/template');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The template has been saved successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $departmentIds = $this->getRequest()->getParam('template');
        if(!is_array($departmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select template(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getModel('helpdesk/template')->load($departmentId);
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
        $departmentIds = $this->getRequest()->getParam('template');
        if(!is_array($departmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select template(s)'));
        } else {
            try {
                foreach ($departmentIds as $departmentId) {
                    $department = Mage::getSingleton('helpdesk/template')
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
        $fileName   = 'template.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_gateway_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'template.xml';
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
}
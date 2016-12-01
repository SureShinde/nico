<?php

class MW_HelpDesk_Adminhtml_Hdadmin_TicketlogController extends Mage_Adminhtml_Controller_Action
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
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('helpdesk/ticketlog');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
		        
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
        $ticketlogs = $this->getRequest()->getParam('ticketlog');
        if(!is_array($ticketlogs)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select ticket log(s)'));
        } else {
            try {
                foreach ($ticketlogs as $id_ticketlog) {
                    $ticketlog = Mage::getModel('helpdesk/ticketlog')->load($id_ticketlog);
                    $ticketlog->delete(); 
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) has been deleted successfully', count($ticketlogs)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
//    public function massActiveAction()
//    {
//        $departmentIds = $this->getRequest()->getParam('rule');
//        if(!is_array($departmentIds)) {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select ticket log(s)'));
//        } else {
//            try {
//                foreach ($departmentIds as $departmentId) {
//                    $department = Mage::getSingleton('helpdesk/ticketlog')
//                        ->load($departmentId)
//                        ->setIsActive($this->getRequest()->getParam('active'))
//                        ->save();
//                }
//                $this->_getSession()->addSuccess(
//                    $this->__('Total of %d record(s) has been updated successfully', count($departmentIds))
//                );
//            } catch (Exception $e) {
//                $this->_getSession()->addError($e->getMessage());
//            }
//        }
//        $this->_redirect('*/*/index');
//    }
  
    public function exportCsvAction()
    {
        $fileName   = 'ticketlog.csv';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_ticketlog_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'ticketlog.xml';
        $content    = $this->getLayout()->createBlock('helpdesk/adminhtml_ticketlog_grid')
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
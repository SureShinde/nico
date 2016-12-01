<?php

class Halox_AgeVerification_Adminhtml_AgeverificationController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('ageverification/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    protected function _isAllowed() {
        return true;
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ageverification/ageverification')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {

                $model->setData($data);
            }

            Mage::register('ageverification_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('ageverification/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('ageverification/adminhtml_ageverification_edit'))
                    ->_addLeft($this->getLayout()->createBlock('ageverification/adminhtml_ageverification_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ageverification')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['filename']['name']);
                } catch (Exception $e) {
                    
                }

                //this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }
            $data['pincode'] = '*';

            $model = Mage::getModel('ageverification/ageverification');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ageverification')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ageverification')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('ageverification/ageverification');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $ageverificationIds = $this->getRequest()->getParam('ageverification');
        if (!is_array($ageverificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ageverificationIds as $ageverificationId) {
                    $ageverification = Mage::getModel('ageverification/ageverification')->load($ageverificationId);
                    $ageverification->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($ageverificationIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $ageverificationIds = $this->getRequest()->getParam('ageverification');
        if (!is_array($ageverificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($ageverificationIds as $ageverificationId) {
                    $ageverification = Mage::getSingleton('ageverification/ageverification')
                            ->load($ageverificationId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($ageverificationIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'ageverification.csv';
        $content = $this->getLayout()->createBlock('ageverification/adminhtml_ageverification_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'ageverification.xml';
        $content = $this->getLayout()->createBlock('ageverification/adminhtml_ageverification_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function getStateAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $state = "<option value=''>Please Select</option>";
        $state .= "<option value='*'>All States (*)</option>";
        if ($countrycode != '') {
            $statearray = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countrycode)->load();
            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
        }
        echo $state;
    }

    public function saveCustomerDOBAndSetVerifiedAction() {
        $orderId = $this->getRequest()->getParam('orderId');
        $data['date_of_birth'] = $this->getRequest()->getParam('dob');
        $customerId = $this->getRequest()->getParam('cust_id');
        $orderObj = Mage::getModel('sales/order')->load($orderId);
		try{
			//set customer DOB
			Mage::helper('ageverification')->setCustomerDOB($data, $customerId, 'apifailed');

			//set order status for Age as Verified        
			$this->createLog($customerId, $orderObj, $data['date_of_birth']);
			if (Mage::helper('ageverification')->checkMaxVerificationStep($orderObj) == 3) {
				if ($orderObj->getVerificationStatus() == 'Age Verification Pending') {
				   
				   $orderObj->setVerificationStatus('Age Verified')->save();
					
				   $result = '';
				   $result = Mage::getModel('frauddetection/result')->loadByOrderId($orderId);
				   if(     
						!isset($result['fraud_score']) 
						|| !Mage::getStoreConfig('frauddetection/general/holdwhenflagged') 
						|| $result['fraud_score'] < Mage::getStoreConfig('frauddetection/general/threshold')
					){
					   
					   if($orderObj->canUnhold()){
							$orderObj->unhold();
							$orderObj->save();
					   }
				   }
				   
				}
			}
		 }catch(Exception $e){
            $error = $e->getMessage();
            Mage::helper('ageverification')->createErrorLog($customerId, $orderObj, $error); 
            $this->_getSession()->addError($error);
		}
    }
	
	public function createLog($customerId, $orderObj, $dob){
	     try{
			 $customer = Mage::getModel('customer/customer')->load($customerId);
			 $customerName = $customer->getFirstname()."".$customer->getLastname(); 
			 $orderId = $orderObj->getId();
			 $logString = " Order ".$orderId." DOB Updated as ".$dob." By  ".$customerName." On ".Mage::getModel('core/date')->date('Y-m-d H:i:s');
			 $path = "./media/verifyUploads/dob/";
			 if (!file_exists($path)) {
					mkdir($path, 0777, true);
			 }
			 $log = fopen($path.$orderId."-dob-update.log", "a");
			 fwrite($log, "\n". $logString);
			 fclose($log);
		 }catch(Exception $e){
		  $this->_getSession()->addError($e->getMessage());
		 }
    }

}

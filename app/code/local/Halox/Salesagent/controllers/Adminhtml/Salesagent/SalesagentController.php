<?php

/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */

/**
 * Salesagent admin controller
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Adminhtml_Salesagent_SalesagentController extends Mage_Adminhtml_Controller_Action {
    
	 protected function _isAllowed()
	{
		return true;
		
	}
    protected function _initAgent() {
        $id = (int) $this->getRequest()->getParam('id');
        $agent = Mage::getModel('halox_salesagent/agent');
        if ($id) {
            $agent->load($id);
        }
        Mage::register('current_advertisement', $agent);

        return $agent;
    }
    
    protected function checkDuplicateEmail($email) {
        $id = (int) $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $collection = Mage::getModel('halox_salesagent/agent')
                    ->getCollection()
                    ->addFieldToFilter('email', $email)
                    ->addFieldToFilter('id', array('neq' => $id));
        } else {
            $collection = Mage::getModel('halox_salesagent/agent')
                    ->getCollection()
                    ->addFieldToFilter('email', $email);
        }
        $size = $collection->getSize();
        if ($size > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function indexAction() {
        $this->loadLayout();
        $this->_title('Manage Agents')
                ->_title('Agents');
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction() {
        $data = $this->getRequest()->getPost('agentdata');
		 $isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Edit');
         if(!$isEditAllowed){
           Mage::getSingleton('adminhtml/session')->addError("You do't have enough permission for this action.");
           $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
           return;
        
         }
        if ($data) {
            try {
                $validate = $this->checkDuplicateEmail($data['email']);
                if (!$validate) {
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    Mage::getSingleton('adminhtml/session')->addError('This email Already Exists.');
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                $agent = $this->_initAgent();
                $agent->setName($data['name']);
                $agent->setEmail($data['email']);
                $agent->setPhone($data['phone']);
                $agent->setDescription($data['description']);
                $imageName = $this->_uploadAndGetName('image', Mage::helper('halox_salesagent')->getImageBaseDir(), $data);
                if(!empty($imageName)){
                $agent->setImage($imageName);
                }
                $agent->setStatus($data['status']);
                $agent->save();
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('halox_salesagent')->__('Agent is successfully saved.'));
                //Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $agent->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError('There was a problem saving the Agent Profile.');
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError('Unable to find Agent to save.');
        $this->_redirect('*/*/');
    }

    /**
     * upload file and get the uploaded name
     * @access public
     * @param string $input
     * @param string $destinationFolder
     * @param array $data
     * @return string
     */
    protected function _uploadAndGetName($input, $destinationFolder, $data) {
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = new Varien_File_Uploader($input);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }
        } catch (Exception $e) {
            if ($e->getCode() != Varien_File_Uploader::TMP_NAME_EMPTY) {
                throw $e;
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }

    public function editAction() {
	    $currentId = Mage::app()->getRequest()->getParam('id');
		$action = Mage::app()->getRequest()->getParam('action');
		if($action == 'agent_reply'){
		 Mage::getSingleton('core/session')->setAgentId($currentId);
		}
		$agentSessId = Mage::getSingleton('core/session')->getAgentId();
		if(!empty($agentSessId) && $agentSessId != $currentId){
			Mage::getSingleton('adminhtml/session')->addError("You don't have enough permission to access this.");
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
		}
        $agentData = $this->_initAgent();
        if (!empty($agentData)) {
            $data = $agentData->getData();
            Mage::getSingleton('adminhtml/session')->setFormData($data);
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function deleteAction() {
        try {
		     $isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Delete');
			 if(!$isEditAllowed){
			   Mage::getSingleton('adminhtml/session')->addError("You do't have enough permission for this action.");
			   $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
			   return;			
			 }
            $agentData = $this->_initAgent();
            if (!empty($agentData)) {
                $agentData->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('halox_salesagent')->__('Agent deleted successfully.'));

            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError('There was a problem in deleting the Agent Profile.');
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    /**
     * mass delete agents - action
     * @access public
     * @return void
     */
    public function massDeleteAction() {
        $salesagentIds = $this->getRequest()->getParam('salesagent');
		 $isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Massaction');
         if(!$isEditAllowed){
           Mage::getSingleton('adminhtml/session')->addError("You do't have enough permission for this action.");
           $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
           return;        
         }
        if (!is_array($salesagentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('Please select agents to delete.'));
        } else {
            try {
                foreach ($salesagentIds as $salesagentId) {
                    $agent = Mage::getModel('halox_salesagent/agent');
                    $agent->setId($salesagentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('halox_salesagent')->__('Total of %d agents deleted successfully.', count($salesagentIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('Some Error Occurred while deleting agents.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     * @access public
     * @return void
     */
    public function massStatusAction() {
        $agentIds = $this->getRequest()->getParam('salesagent');
		$isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Massaction');
        if(!$isEditAllowed){
           Mage::getSingleton('adminhtml/session')->addError("You do't have enough permission for this action.");
           $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
           return;        
         }
        if (!is_array($agentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('Please select Agents.'));
        } else {
            try {
                foreach ($agentIds as $agentId) {
                    Mage::getSingleton('halox_salesagent/agent')->load($agentId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d agents updated successfully', count($agentIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('There was an error updating advertisements.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massAssignAgentsAction() {
	    $isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Massaction');
         if(!$isEditAllowed){
           Mage::getSingleton('adminhtml/session')->addError("You do't have enough permission for this action.");
           $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
           return;        
         }
        $customerIds = $this->getRequest()->getParam('customer');
        $agentId = $this->getRequest()->getParam('agent');
        if (!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('Please select Customers.'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setSalesRep($agentId);
                    $customer->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d customer agent updated successfully', count($customerIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('halox_salesagent')->__('There was an error updating advertisements.'));
                Mage::logException($e);
            }
        }
        //$this->_redirect('*/*/');
        $this->_redirectReferer();
        return;
    }

    /**
     * export as csv - action
     * @access public
     * @return void
     */
    public function exportCsvAction() {
        $fileName = 'advertisement.csv';
        $content = $this->getLayout()->createBlock('halox_salesagent/adminhtml_salesagent_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     * @access public
     * @return void
     */
    public function exportExcelAction() {
        $fileName = 'agentList.xls';
        $content = $this->getLayout()->createBlock('halox_salesagent/adminhtml_salesagent_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     * @access public
     * @return void
     */
    public function exportXmlAction() {
        $fileName = 'agentList.xml';
        $content = $this->getLayout()->createBlock('halox_salesagent/adminhtml_salesagent_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function loadChatAction(){
        
        $this->loadLayout();
        $this->renderLayout();
   }
   
   public function sendMessageAction(){
    try{
             $message = $this->getRequest()->getPost('message');
             if(!empty($message)){
             $cid = $this->getRequest()->getParam('cid');
             $customer = Mage::getModel('customer/customer')->load($cid);
             $salesRepId = $customer->getSalesRep();
             $customerId = $customer->getId();
             $model = Mage::getModel('halox_salesagent/message');
             $model->setMessage($message);
             $model->setCustomerId($customerId);
             $model->setSalesAgentsId($salesRepId);
             $model->setSentBy('agent');
             $model->setSeen(0);
             $model->setStatus(1);
             $data['content'] = $message;
             $data['customer'] = $customer;
             $data['salesRepId'] = $salesRepId;
             $this->sendMailMessage($data);
             $model->setSentAt(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
             $model->save();
             Mage::getSingleton('core/session')->addSuccess("Message Sent Successfully.");
             }else{
             Mage::getSingleton('core/session')->addError("Please Enter Some text to send.");   
             }
             $result = array('result'=>1);
             $jsonData = Mage::helper('core')->jsonEncode($result);
             echo $jsonData;
           } catch (Exception $e) {
                $result = array('result'=>0);
                $jsonData = Mage::helper('core')->jsonEncode($result);
                echo $jsonData;
                Mage::getSingleton('core/session')->addError(Mage::helper('halox_salesagent')->__('There was an error in sending Message. Please try Again later.'));
                Mage::logException($e);
		}
   
   }
   
    public function sendMailMessage($data){            
              try{
                                
                $agent = Mage::getModel('halox_salesagent/agent')->load($data['salesRepId']);
                $customer = $data['customer'];
                $agentEmail = $agent->getEmail();               
                $ccEmails = Mage::getStoreConfig('salesagent/mails_group/cc_mails', Mage::app()->getStore());                 
                $ccEmailsArray = array();
                $ccEmailsArray = explode(';', $ccEmails);
                
                $customerEmail = $customer->getEmail();                
                /*** mail section starts here ***/
                $email = Mage::getModel('core/email_template');
                $emailTemplate  = $email->loadDefault('agent_reply');   
                
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject('New Message');
                $emailTemplateVariables = array();
                $emailTemplateVariables['customerName'] = $customer->getFirstname(). ' ' .$customer->getLastname();
                $emailTemplateVariables['content'] = $data['content'];
                $emailTemplateVariables['agentName'] = $agent->getName();
                $storeId = $customer->getStoreId();				
                $emailTemplateVariables['replyBaseUrl'] = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                //$emailTemplate->getProcessedTemplate($emailTemplateVariables);
                Mage::log($emailTemplateVariables['replyBaseUrl']."==");
                $emailTemplate->setSenderEmail($agentEmail);
                $emailTemplate->setSenderName($agent->getName());
                $emailTemplate->getMail()->addCc($ccEmailsArray);
                $templateId = Mage::getStoreConfig('salesagent/mails_group/salesagent_email_template', Mage::app()->getStore());
                 if(!empty($templateId)){
                     $email->sendTransactional($templateId, array('name' => $agent->getName(),'email' =>$agentEmail ), $customerEmail , $emailTemplateVariables['customerName'], $emailTemplateVariables,  Mage::app()->getStore());
                    }else{
                    $emailTemplate->send($customerEmail, $customer->getName(), $emailTemplateVariables);
                 }
                }
                catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('Unable to send mail.');
                Mage::log('Salesagent Email Error'.$e->getMessage(), null, 'salesagent.log');
                }
               
        }
   
}

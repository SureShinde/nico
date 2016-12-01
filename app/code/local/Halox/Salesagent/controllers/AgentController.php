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

class Halox_Salesagent_AgentController
    extends Mage_Core_Controller_Front_Action {
	public function indexAction(){
	    if( !Mage::getSingleton('customer/session')->isLoggedIn() ){
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }
	$this->loadLayout(); 
	$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('salesagent/agent');
        }
        $this->renderLayout();
	}
        protected function _initMessage() {
        $id = (int) $this->getRequest()->getParam('id');
        $agent = Mage::getModel('halox_salesagent/message');
        if ($id) {
            $agent->load($id);
        }
        Mage::register('current_advertisement', $agent);

        return $agent;
        }
        public function sendMessageAction(){
            try{
             $message = $this->getRequest()->getPost('message');
             if(!empty($message)){
             $customer = Mage::getSingleton('customer/session')->getCustomer();
             $salesRepId = $customer->getSalesRep();
             $customerId = $customer->getId();
             $model = $this->_initMessage();
             $model->setMessage($message);
             $model->setCustomerId($customerId);
             $model->setSalesAgentsId($salesRepId);
             $model->setSentBy('customer');
             $model->setSeen(0);
             $model->setStatus(1);
             $model->setSentAt(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
             $model->save();
             $data['content'] = $message;
             $data['customer'] = $customer;
             $data['salesRepId'] = $salesRepId;
             $this->sendMailMessage($data);
             Mage::getSingleton('core/session')->addSuccess("Message Sent Successfully.");
             }else{
             Mage::getSingleton('core/session')->addError("Please Enter Some text to send.");   
             }
             $this->_redirectReferer();
             return;
           } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('halox_salesagent')->__('There was an error in sending Message. Please try Again later.'));
                Mage::logException($e);
            }
        }
        
        public function sendMailMessage($data){            
              try{
                $email = Mage::getModel('core/email_template');
                $emailTemplate  = $email->loadDefault('agent_contact');                
                $agent = Mage::getModel('halox_salesagent/agent')->load($data['salesRepId']);
                $customer = $data['customer'];
                $emailTemplateVariables = array();
                $emailTemplateVariables['customerName'] = $customer->getFirstname(). ' ' .$customer->getLastname();
                $emailTemplateVariables['content'] = $data['content'];
                $emailTemplateVariables['agentName'] = $agent->getName();
                $emailTemplateVariables['adminUri'] = Mage::getStoreConfig('salesagent/mails_group/admin_url', Mage::app()->getStore());          
				$emailTemplateVariables['agentId'] = $agent->getId();
				$emailTemplateVariables['customerId'] = $customer->getId();			  
				$agentEmail = $agent->getEmail();
                $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->setSenderEmail($customer->getEmail());
                $emailTemplate->setSenderName($emailTemplateVariables['customerName']);
                $ccEmails = Mage::getStoreConfig('salesagent/mails_group/cc_mails', Mage::app()->getStore());                 
                $ccEmailsArray = explode(';', $ccEmails);
                $emailTemplate->getMail()->addCc($ccEmailsArray);
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject('New Message');
                $templateId = Mage::getStoreConfig('salesagent/mails_group/salesagent_email_template', Mage::app()->getStore());
                if(!empty($templateId)){
                 $email->sendTransactional($templateId, array('name' => $emailTemplateVariables['customerName'],'email' => $customer->getEmail()), $agentEmail, $agent->getName(), $emailTemplateVariables , Mage::app()->getStore());
                }else{
                 $emailTemplate->send($agentEmail, $agent->getName(), $emailTemplateVariables);
                }
                }
                catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('Unable to send mail.');
                Mage::log('Salesagent Email Error'.$e->getMessage(), null, 'salesagent.log');
                }
               
        }
}
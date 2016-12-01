<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Agent model
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Model_Observer{
   
    public function redirectAdminToCustomPage(){            
            $action = Mage::app()->getRequest()->getParam('action');
            if($action == 'agent_reply'){
            $id = Mage::app()->getRequest()->getParam('id');
            $customerId = Mage::app()->getRequest()->getParam('cid');
            $path = Mage::helper('adminhtml')->getUrl('adminhtml/salesagent_salesagent/edit/id/'.$id.'/cid/'.$customerId);
            Mage::app()->getResponse()->setRedirect($path);            
            Mage::app()->getResponse()->sendResponse();  
            exit;
            }
    }
    public function redirectCustomerToCustomPage(){            
            $action = Mage::app()->getRequest()->getParam('action');
			$this->notifySalesRep();
            if($action == 'customer_reply'){
            $path = 'salesagent/agent/action/agent_reply';
            Mage::app()->getResponse()->setRedirect($path);            
            Mage::app()->getResponse()->sendResponse();  
            exit;
            }
    }
	
	public function notifySalesRep(){
	     $isEnabled = Mage::getStoreConfig(
                   'salesagent/check/enabled',
                   Mage::app()->getStore()
               ); 
	     if($isEnabled == 1){
         $customerData = Mage::getSingleton('customer/session')->getCustomer();
         $salesRepId =  $customerData->getSalesRep();
          $salesRepEmail = '';
          if(!empty($salesRepId)){
                $data['salesRepId'] = $salesRepId;
                $data['customer'] = $customerData;
                $this->sendMailMessage($data);
			}
		}
    }
    
    public function sendMailMessage($data){            
              try{
                $email = Mage::getModel('core/email_template');
                $emailTemplate  = $email->loadDefault('notify_agent');                
                $agent = Mage::getModel('halox_salesagent/agent')->load($data['salesRepId']);
                $customer = $data['customer'];
                $emailTemplateVariables = array();
                $emailTemplateVariables['customerName'] = $customer->getFirstname(). ' ' .$customer->getLastname();
                $emailTemplateVariables['content'] = $data['content'];
                $emailTemplateVariables['customerEmail'] = $customer->getEmail();
                $emailTemplateVariables['agentName'] = $agent->getName();
                $emailTemplateVariables['agentId'] = $agent->getId();
                $emailTemplateVariables['customerId'] = $customer->getId();			  
                $agentEmail = $agent->getEmail();
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->setSenderEmail($customer->getEmail());
                $emailTemplate->setSenderName($emailTemplateVariables['customerName']);    
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject('Wholesale Customer Login Notification');                
                $emailTemplate->send($agentEmail, $agent->getName(), $emailTemplateVariables);
                }
                catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('Unable to send mail.');
                Mage::log('Salesagent Email Error'.$e->getMessage(), null, 'salesagent.log');
                }
               
        }
}

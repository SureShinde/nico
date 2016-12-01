<?php

class MW_Helpdesk_ContactController extends Mage_Core_Controller_Front_Action {

    public function sentAction() {

        if ($data = $this->getRequest()->getPost()) {

            if (Mage::getSingleton('helpdesk/spam')->checkSpam($data['sender'])) {
                Mage::getSingleton('customer/session')->addError($this->__("Unable submit ticket"));
                $this->_redirect('helpdesk/account/submit/');
                return;
            }
            //reformat $_FILES into supported ZendFramework format
            $file_attachment = Mage::helper('helpdesk')->processMultiUpload();
            if ($file_attachment) {
                $data['file_attachment'] = $file_attachment;
            }
            //echo "<pre>";var_dump($data);die();
            try {
                $ticket = Mage::getModel('helpdesk/ticket');
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $data['name'] = Mage::getSingleton('customer/session')->getCustomer()->getFirstname() . ' '
                            . Mage::getSingleton('customer/session')->getCustomer()->getLastname();
                    $data['sender'] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
                }
                $ticket->saveTicket($data);
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket was submitted and will be responded to as soon as possible. Thank you for contacting us'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
            }
            $this->_redirect('contacts');
        }
    }
 protected function _isAllowed()
    {
        return true;
    }
}

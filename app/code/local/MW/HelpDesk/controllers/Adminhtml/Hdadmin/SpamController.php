<?php
class MW_HelpDesk_Adminhtml_Hdadmin_SpamController extends Mage_Adminhtml_Controller_Action {
    public function indexAction(){
        
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append(
                $this->getLayout()->createBlock('helpdesk/adminhtml_spam'));
        $this->renderLayout();
    }
    protected function _isAllowed()
{
    return true;
}

     public function markSpamAction() {
        if ($this->getRequest()->has('id')) {
            $ticket = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));

            $spam = Mage::getModel('helpdesk/spam');
            $spam->setEmail($ticket->getSender());
            try{
                $spam->save();
                $this->_getSession()->addSuccess(
                    $this->__('Mark this email as spam successfully')
                );
                $this->_redirect('*/adminhtml_ticket/view', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
                return;
            }catch(Zend_Db_Statement_Exception $dbStateExcep ){
                if($dbStateExcep->getCode() == '23000'){
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('This email is marked as spam'));
                }else{
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            $this->_redirect('*/adminhtml_ticket/index');
        }
    }
    
    public function massDeleteAction(){
        $emailIds = $this->getRequest()->getParam('id');

        if (!is_array($emailIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Please select email(s)'));
        } else {
            try {
                foreach ($emailIds as $emailId) {
                    $email = Mage::getModel('helpdesk/spam')->load($emailId);
                    $email->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) has been deleted successfully', count($emailIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
?>

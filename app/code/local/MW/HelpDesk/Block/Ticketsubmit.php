<?php
class MW_HelpDesk_Block_Ticketsubmit extends Mage_Core_Block_Template{
    private $orders = null;
    
    public function _construct() {
        parent::_construct();
        $this->setTemplate('mw_helpdesk/ticketsubmit.phtml');
    }
    
    public function isRequiredLogin(){
        if($this->getRequest()->getParam('id')){
            $department = Mage::getModel('helpdesk/department')->load($this->getRequest()->getParam('id'));
            if(intval($department->getRequiredLogin()) === 1){
                return true;
            }
        }
        return false;
    }
    
    public function hasOrders(){
        $orders = $this->getOrders();
        if($orders->getSize() > 0){
            return true;
        }
        return false;
    }
    
    public function getOrders(){
        if(is_null($this->orders)){
            $collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('increment_id', 'DESC');
            $this->orders = $collection;
        }
        return $this->orders;
    }
    
    public function getMultiFileUploader(){
        return $this->getLayout()->createBlock('helpdesk/multipleuploader')->toHtml();
    }
    
}
?>

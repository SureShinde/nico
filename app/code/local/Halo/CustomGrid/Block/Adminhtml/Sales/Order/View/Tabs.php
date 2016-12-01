<?php

class Halo_CustomGrid_Block_Adminhtml_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {  
         parent::_construct();
        $this->setTemplate('customgrid/order/view/tab/custom_tab.phtml');
    }
    public function getTabLabel() {
        return $this->__('Related Transactions');
    }
    public function getTabTitle() {
        return $this->__('Related Transactions Detail');
    }
    public function canShowTab() {
        return true;
    }
    public function isHidden() {
        return false;
    }
    public function getOrder(){
        return Mage::registry('current_order');
    }
    public function getUsaepaydata(){
        $orderid = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);   
        
        $dbConf = Mage::getConfig()->getNode('global/resources/default_setup/connection');
        $db = new Zend_Db_Adapter_Pdo_Mysql(array('host'=> $dbConf->host, 'username' => $dbConf->username, 'password' => $dbConf->password, 'dbname'=> $dbConf->dbname));
        $sql        = "Select * from usaepay_log where invoice=".$order->getData('increment_id');
        $rows       = $db->fetchAll($sql);
        return $rows;
    }
}
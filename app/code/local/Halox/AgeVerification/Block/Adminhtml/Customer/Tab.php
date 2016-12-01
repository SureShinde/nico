<?php

class Halox_AgeVerification_Block_Adminhtml_Customer_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function _construct() {
        parent::_construct();
        $this->setTemplate('ageverification/customer/tab.phtml');
    }

    public function getTabLabel() {
        return $this->__('Age Verification');
    }


    public function getTabTitle() {
        return $this->__('Click here to view your age ageverification content');
    }


    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}

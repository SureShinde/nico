<?php

class Halox_Pageprint_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isPrintpageEnable() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('printpage_general/pageprint_options/pageprint_enabled', $storeId);
    }

    public function isPrintOnCartEnabled() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('printpage_general/pageprint_options/pageprint_cart_enabled', $storeId);
    }
    
    public function getLogoPath() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('printpage_general/pageprint_options/pageprint_logo', $storeId);
    }
    
    public function getAddressText() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('printpage_general/pageprint_options/pageprint_store_address', $storeId);
    }

}

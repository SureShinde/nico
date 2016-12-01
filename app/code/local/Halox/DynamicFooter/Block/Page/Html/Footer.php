<?php

class Halox_DynamicFooter_Block_Page_Html_Footer extends Mage_Page_Block_Html_Footer
{

	/**
     * save cache per customer basis
     */
	public function getCacheKeyInfo()
    {
        $cacheKey = array(
            'PAGE_FOOTER',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->isLoggedIn()
        );

        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $cacheKey[] = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }

        return $cacheKey;
    }

}
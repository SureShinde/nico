<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Salesagent image field renderer helper
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Helper_Image
    extends Varien_Data_Form_Element_Image {
    /**
     * get the url of the image
     * @access protected
     * @return string
          */
    protected function _getUrl(){
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('halox_salesagent/salesagent_image')->getImageBaseUrl().$this->getValue();
		
        }
        return $url;
    }
}

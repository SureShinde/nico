<?php
/**
 * @category   Apptrian
 * @package    Apptrian_ImageOptimizer
 * @author     Apptrian
 * @copyright  Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_ImageOptimizer_Block_Adminhtml_Button_Empty extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Import static blocks
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	
        $buttonLabel = Mage::helper('apptrian_imageoptimizer')->__('Empty Index');
        
   		$url = Mage::helper('adminhtml')->getUrl(
   				'adminhtml/apptrian_imgopt/empty');
   		
    	$html = $this->getLayout()->createBlock('adminhtml/widget_button')
	    	->setType('button')
	    	->setClass('apptrian-imageoptimizer-admin-button-empty')
	    	->setLabel($buttonLabel)
	    	->setOnClick("setLocation('$url')")
	    	->toHtml();
	    	
    	return $html;
    	
    }
}

<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

class Emjainteractive_ShippingOption_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected $_objectId = 'option_id';
	
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'emjainteractive_shippingoption';

        $this->_updateButton('save', 'label', Mage::helper('emjainteractive_shippingoption')->__('Save Option'));
        $this->_updateButton('delete', 'label', Mage::helper('emjainteractive_shippingoption')->__('Delete Option'));
        
        $this->_addScripts();
    }

    public function getHeaderText()
    {
        if( Mage::registry('option_data') && Mage::registry('option_data')->getId() ) {
            return Mage::helper('emjainteractive_shippingoption')->__("Edit Option '%s'", $this->htmlEscape(Mage::registry('option_data')->getLabel()));
        } else {
            return Mage::helper('emjainteractive_shippingoption')->__('New Option');
        }
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }
    
    protected function _addScripts()
    {
    	$this->_formScripts[] = "
        	function checkOptionType(obj) {
                if( $('type').value == 'radio' || $('type').value == 'select' ) {
                    $('options').parentNode.parentNode.style.display = '';
                } else {
                    $('options').parentNode.parentNode.style.display = 'none';
                }
            }
            
            checkOptionType();
    	";
    }
}
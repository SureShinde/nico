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

class Emjainteractive_ShippingOption_Block_Renderer_Abstract extends Mage_Core_Block_Text
{
    protected function _getElementId()
    {
    	return $this->getCarrier() . '_' . $this->getOption()->getCode();
    }
    
    protected function _getElementName()
    {
    	return 'emjainteractive_shippingoption[' . $this->getCarrier() . '][' . $this->getOption()->getCode() . ']';
    }
    
    protected function _getIsRequired()
    {
        return $this->getOption()->getIsRequired();
    }
    
    public function _toHtml()
    {
    	$html = '<td>'; 
        $html.= $this->_renderLabel();
        $html.= '</td>';
        $html.= '<td>';
        $html.= $this->_renderElement();
        $html.= '</td>';
        return $html;
    }
    
    protected function _renderLabel()
    {
    	return '';
    }
    
    protected function _renderElement()
    {
    	return '';
    }
}
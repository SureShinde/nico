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

class Emjainteractive_ShippingOption_Block_Adminhtml_Sales_Order_Options extends Mage_Core_Block_Template 
{
	protected $_order;
	protected $_options;
	protected $_carrier;
	
	public function __construct()
	{
		$this->setTemplate('emjainteractive/shipingoption/sales/order/options.phtml');
	}
	
	public function setOrder($order)
	{
		$this->_order = $order;
		if( !$order->getShippingMethod() || !$this->_getOrder()->getEmjainteractiveShippingoption() ) {
			$this->_options = array();
		}

		$methodParts = explode('_', $order->getShippingMethod());
		$this->_carrier = array_shift($methodParts);
		
		return $this;
	}
	
	protected function _getOrder()
	{
		return $this->_order;
	}
	
	protected function _getOptions()
	{
		if( !$this->_options ) {
			$options = unserialize($this->_getOrder()->getEmjainteractiveShippingoption());
			if( isset($options[$this->_carrier]) ) {
				$_options = array();
				$model = Mage::getSingleton('emjainteractive_shippingoption/option');
				foreach ($options[$this->_carrier] as $_code => $_value) {
					$model->load($_code, 'code');
					if( $model->getId() ) {
						$_options[] = array('label' => $model->getLabel(), 'value' => $_value);
					}
				}
			} else {
				$_options = array();
			}
			
			$this->_options = $_options;
		}
		
		return $this->_options;
	}
}
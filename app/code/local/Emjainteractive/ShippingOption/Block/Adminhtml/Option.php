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

class Emjainteractive_ShippingOption_Block_Adminhtml_Option extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
    	$this->_blockGroup = 'emjainteractive_shippingoption';
        $this->_controller = 'adminhtml';
        $this->_headerText = Mage::helper('emjainteractive_shippingoption')->__('Shipping Options Manager');
        $this->_addButtonLabel = Mage::helper('emjainteractive_shippingoption')->__('Add New Option');
        parent::__construct();
    }
}
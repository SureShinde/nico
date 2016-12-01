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

class Emjainteractive_ShippingOption_Model_Mysql4_Option extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('emjainteractive_shippingoption/option', 'option_id');
    }
    
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('code'),
            'title' => Mage::helper('emjainteractive_shippingoption')->__('Option with the same code')
        ));
        return $this;
    }
}
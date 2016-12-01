<?php
/**
 * Halox_Payment extension
 * 
 * @category       Halox
 * @package        Halox_Payment
 * @copyright      Copyright (c) 2016
 */
/**
 *Payment Info Block
 *
 * @category    Halox
 * @package     Halox_Payment
 */

/**
 * Base payment iformation block
 *
 */
class Halox_Payment_Block_Info extends Mage_Core_Block_Template
{
    /**
     * Payment rendered specific information
     *
     * @var Varien_Object
     */
    protected $_paymentSpecificInformation = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/info.phtml');
    }

}

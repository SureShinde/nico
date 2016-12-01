<?php
/**
 * Halox_Payment extension
 * 
 * @category       Halox
 * @package        Halox_Payment
 * @copyright      Copyright (c) 2016
 */
/**
 *Achcredit Model
 *
 * @category    Halox
 * @package     Halox_Payment
 */

/**
 * Bank Transfer payment method model
 */
class Halox_Payment_Model_Method_Achcredit extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_METHOD_BANKTRANSFER_CODE = 'achcredit';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_BANKTRANSFER_CODE;

    /**
     * Bank Transfer payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'haloxpayment/form_achcredit';
    protected $_infoBlockType = 'haloxpayment/info_achcredit';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

}

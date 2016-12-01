<?php
/**
 * Halox_Payment extension
 * 
 * @category       Halox
 * @package        Halox_Payment
 * @copyright      Copyright (c) 2016
 */
/**
 *Achdebit Model
 *
 * @category    Halox
 * @package     Halox_Payment
 */

/**
 * Bank Transfer payment method model
 */
class Halox_Payment_Model_Method_Achdebit extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_METHOD_BANKTRANSFER_CODE = 'achdebit';

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
    protected $_formBlockType = 'haloxpayment/form_achdebit';
    protected $_infoBlockType = 'haloxpayment/info_achdebit';

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

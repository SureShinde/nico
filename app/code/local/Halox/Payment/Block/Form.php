<?php
/**
 * Halox_Payment extension
 * 
 * @category       Halox
 * @package        Halox_Payment
 * @copyright      Copyright (c) 2016
 */
/**
 *Payment Block
 *
 * @category    Halox
 * @package     Halox_Payment
 */

/**
 * Payment method form base block
 */
class Halox_Payment_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof Mage_Payment_Model_Method_Abstract)) {
            Mage::throwException($this->__('Cannot retrieve the payment method model object.'));
        }
        return $method;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * Retrieve field value data from payment info object
     *
     * @param   string $field
     * @return  mixed
     */
    public function getInfoData($field)
    {
        return $this->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
    }

    /**
     * Check whether current payment method can create billing agreement
     *
     * @return bool
     */
    public function canCreateBillingAgreement()
    {
        return $this->getMethod()->canCreateBillingAgreement();
    }
}

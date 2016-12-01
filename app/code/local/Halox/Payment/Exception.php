<?php
/**
 * Halox_Payment extension
 * 
 * @category       Halox
 * @package        Halox_Payment
 * @copyright      Copyright (c) 2016
 */
/**
 *Exception Handler
 *
 * @category    Halox
 * @package     Halox_Payment
 */

class Halox_Payment_Exception extends Exception
{
    protected $_code = null;

    public function __construct($message = null, $code = 0)
    {
        $this->_code = $code;
        parent::__construct($message, 0);
    }

    public function getFields()
    {
        return $this->_code;
    }
}

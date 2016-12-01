<?php
/*
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 */

class Fooman_Surcharge_Model_Paypal_Cart extends Mage_Paypal_Model_Cart {

    protected function _render()
    {
        parent::_render();
        $this->updateTotal(self::TOTAL_SUBTOTAL, $this->_salesEntity->getBaseFoomanSurchargeAmount());
        $this->updateTotal(self::TOTAL_TAX, $this->_salesEntity->getBaseFoomanSurchargeTaxAmount());
    }
}


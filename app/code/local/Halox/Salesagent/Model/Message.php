<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Agent model
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Model_Message
    extends Mage_Core_Model_Abstract {
   
    public function _construct(){
        parent::_construct(); 
        $this->_init('halox_salesagent/message');
    }
   
}

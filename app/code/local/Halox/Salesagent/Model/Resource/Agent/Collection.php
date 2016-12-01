<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Agent collection resource model
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Model_Resource_Agent_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * constructor
     * @access public
     * @return void
     */
    protected function _construct(){ 
        parent::_construct(); 
        $this->_init('halox_salesagent/agent');
    }
}

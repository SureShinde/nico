<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2014
 */
/**
 * Agent model
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Model_Resource_Message
    extends Mage_Core_Model_Resource_Db_Abstract {
   
    public function _construct(){ 
        $this->_init('halox_salesagent/message','id'); 
        
    }
   
}

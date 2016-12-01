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
class Halox_Salesagent_Model_Attribute_Source_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	
    public function getAllOptions()
    {
	   $agentList    = Mage::getModel('halox_salesagent/agent')->getCollection()
                           ->addFieldToFilter('status', '1');
       $items[] = array('label'=>'Select','value'=>'');

        if (is_null($this->_options)) {
		foreach($agentList as $agent){
                $items[] = array('label'=>$agent->getName(),'value'=>$agent->getId());
			}
			$this->_options = $items;
        }
        return $this->_options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
	 
}
?>
 

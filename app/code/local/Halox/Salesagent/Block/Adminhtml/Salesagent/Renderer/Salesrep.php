<?php
class Halox_Salesagent_Block_Adminhtml_Salesagent_Renderer_Salesrep extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
public function render(Varien_Object $row)
{
	$salesRepId = $row->getData($this->getColumn()->getIndex());
	$value = '';
	$agent = Mage::getModel('halox_salesagent/agent');          
	if ($salesRepId ) {
		$agentData = $agent->load($salesRepId);
		$value = $agentData->getName();
	}
	
	return $value;
 
}
 
}

?>
<?php
class Halox_Taxexcisereport_Block_Taxexcisereport extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTaxexcisereport()     
     { 
        if (!$this->hasData('taxexcisereport')) {
            $this->setData('taxexcisereport', Mage::registry('taxexcisereport'));
        }
        return $this->getData('taxexcisereport');
        
    }
}
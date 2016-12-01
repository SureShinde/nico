<?php
class Halox_Pageprint_Block_Pageprint extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPageprint()     
     { 
        if (!$this->hasData('pageprint')) {
            $this->setData('pageprint', Mage::registry('pageprint'));
        }
        return $this->getData('pageprint');
        
    }
}
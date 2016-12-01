<?php
class Halox_AgeVerification_Block_Ageverification extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getVerification()     
     { 
        if (!$this->hasData('ageverification')) {
            $this->setData('ageverification', Mage::registry('ageverification'));
        }
        return $this->getData('ageverification');
        
    }
}
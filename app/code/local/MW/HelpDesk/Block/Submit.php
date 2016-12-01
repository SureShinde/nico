<?php

class MW_HelpDesk_Block_Submit extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	
	public function getDepartmentData()
    {
      return Mage::getModel('helpdesk/department')->getCollection()
  			->addFieldToFilter('active', array('eq' => 1));  
    }
}
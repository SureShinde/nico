<?php
class MW_Helpdesk_Block_Show extends Mage_Core_Block_Template
{
	
	protected $_collection;
	
	public function __construct()
    {   
        $this->_collection = Mage::getModel('helpdesk/ticket')->getCollection()
					->addFilter('sender',Mage::getSingleton('customer/session')->getCustomer()->getEmail())
					->setOrder('ticket_id');
    }
    
    public function count()
    {
        return $this->_collection->getSize();
    }
	
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
	public function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'helpdesk_transaction.toolbar')
					   ->setCollection($this->_getCollection());	// set data for navigation
        
		$this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }
    
	public function getStatus($status)
	{
		return MW_HelpDesk_Model_Config_Source_Status::getLabel($status);
	}
	
	public function getPriority($priority)
	{
		return MW_HelpDesk_Model_Config_Source_Priority::getLabel($priority);
	}
	
	
	public function getDepartment($department)
	{
		$collection =  Mage::getResourceModel('helpdesk/department_collection')
            ->addFieldToFilter('department_id', array('eq'=>$department));
        foreach ($collection as $department)
        	return $department->getName();
	}
}
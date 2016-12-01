<?php
class MW_HelpDesk_Block_Dayoff extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('day_value', array(
            'label' => Mage::helper('helpdesk')->__('Day'),
            'style' => 'width:120px',
        ));
		$this->addColumn('day_title', array(
            'label' => Mage::helper('helpdesk')->__('Title'),
            'style' => 'width:200px',
        ));	
						 
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Day Off');
        parent::__construct();
    }
}
<?php
class MW_HelpDesk_Block_Categoryresponse extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('id_category', array(
            'label' => Mage::helper('helpdesk')->__('ID'),
            'style' => 'width:120px',
        	'default'   =>    'default',
        ));
		$this->addColumn('name_category', array(
            'label' => Mage::helper('helpdesk')->__('Category Name'),
            'style' => 'width:200px',
			'default'   =>    'Default'
        ));	
						 
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Category Template');
        parent::__construct();
    }
}
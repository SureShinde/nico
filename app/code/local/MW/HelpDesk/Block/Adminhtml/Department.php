<?php
class MW_HelpDesk_Block_Adminhtml_Department extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_department';
    $this->_blockGroup = 'helpdesk';	// module name
    $this->_headerText = Mage::helper('helpdesk')->__('Manage Departments');
    $this->_addButtonLabel = Mage::helper('helpdesk')->__('Add Department');
    
    $this->_addButton('save', array(
            'label'     => Mage::helper('helpdesk')->__('Save Order'),
            //'onclick'   => 'setLocation(\'' . $this->save_order() .'\')',
    		'onclick'   => 'save_order()',
			'id'		=> 'department_sort_order',
    ));
    
    parent::__construct();
  }
    
	public function save_order(){
    	return $this->getUrl('*/*/saveorder');
    }
}
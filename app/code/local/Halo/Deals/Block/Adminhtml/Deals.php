<?php


class Halo_Deals_Block_Adminhtml_Deals extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_deals";
	$this->_blockGroup = "deals";
	$this->_headerText = Mage::helper("deals")->__("Voucher Information");
    
	//$this->_addButtonLabel = Mage::helper("deals")->__("Add New Item");
	parent::__construct();
    $this->_removeButton('add'); 
	}

}
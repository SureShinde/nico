<?php
class MW_HelpDesk_Adminhtml_Hdadmin_StatisticController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('helpdesk/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}   
 protected function _isAllowed()
{
    return true;
}
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
}
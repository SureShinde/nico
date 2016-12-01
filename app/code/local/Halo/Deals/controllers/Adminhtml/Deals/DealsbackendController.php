<?php
class Halo_Deals_Adminhtml_Deals_DealsbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Deals"));
	   $this->renderLayout();
    }
}
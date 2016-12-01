<?php
/**
 * Mas_Mascnotes extension by Makarovsoft.com
 * 
 * @category   	Mas
 * @package		Mas_Mascnotes
 * @copyright  	Copyright (c) 2014
 * @license		http://makarovsoft.com/license.txt
 * @author		makarovsoft.com
 */
/**
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function preDispatch()
    {
        parent::preDispatch();
        
		if (!Mage::getStoreConfigFlag('mascnotes/customer/enabled')) {
            $this->norouteAction();
            return;
        } 
        
        $session = Mage::getSingleton('customer/session');
        if (!$session->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    } 
    protected function _isAllowed() {
       return true;
    }
	public function indexAction()
	{
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        $block = $this->getLayout()->getBlock('customer_account_navigation');
        
        if ($block) {
            $block->setActive('mascnotes/index');
        }
        
        $this->_title(Mage::helper('mascnotes')->__('Admin Notes'));
			 
        $this->renderLayout();
	}
}
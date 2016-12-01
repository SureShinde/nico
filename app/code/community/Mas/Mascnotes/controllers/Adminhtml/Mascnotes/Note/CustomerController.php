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
 * Note product admin controller
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
require_once ("Mage/Adminhtml/controllers/CustomerController.php");
class Mas_Mascnotes_Adminhtml_Mascnotes_Note_CustomerController extends Mage_Adminhtml_CustomerController{
	/**
	 * construct
	 * @access protected
	 * @return void
	 * 
	 */
	protected function _construct(){	
		$this->setUsedModuleName('Mas_Mascnotes');
	}
	/**
	 * notes in the catalog page
	 * @access public
	 * @return void
	 * 
	 */
     protected function _isAllowed() {
       return true;
    }
	public function notesAction(){
		$this->loadLayout();		
		$this->renderLayout();
	}	
}
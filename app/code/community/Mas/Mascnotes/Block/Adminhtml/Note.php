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
 * Note admin block
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Note extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * 
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_note';
		$this->_blockGroup 		= 'mascnotes';
		$this->_headerText 		= Mage::helper('mascnotes')->__('Note');
		$this->_addButtonLabel 	= Mage::helper('mascnotes')->__('Add Note');
		parent::__construct();
	}
}
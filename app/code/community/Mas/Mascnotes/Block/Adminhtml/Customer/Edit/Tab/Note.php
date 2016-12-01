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
 * Note tab on product edit form
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Customer_Edit_Tab_Note extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * Set grid params
	 * @access protected
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();
        $this->setTemplate('mas_mascnotes/notes.phtml');		
	}
	/**
	 * prepare the note collection
	 * @access protected 
	 * @return Mas_Mascnotes_Block_Adminhtml_Catalog_Product_Edit_Tab_Note
	 * 
	 */
	protected function _prepareCollection() {
		$collection = Mage::getResourceModel('mascnotes/note_collection');
		if ($this->getCustomerId()){
			$constraint = 'related.customer_id='.$this->getCustomerId();
			}
			else{
				$constraint = 'related.customer_id=0';
			}
		$collection->getSelect()->joinLeft(
			array('related'=>$collection->getTable('mascnotes/note_customer')),
			'related.note_id=main_table.entity_id',
			array('position')
		);
		$collection->getSelect()->joinLeft(
			array('user'=>$collection->getTable('admin/user')),
			'user.user_id=main_table.user_id',
			array('firstname', 'lastname')
		);
		$collection->getSelect()->where($constraint);
		
		$this->setCollection($collection);
		//parent::_prepareCollection();
		return $this;
	}
	
	public function getCustomerId(){
		$r = Mage::app()->getRequest();
		if ($r->getParam('is_order')) {
			$this->setData('is_order', 1);
		}
		if ($r->getParam('customer_id')) {
			return $r->getParam('customer_id');
		}
	}
	
}
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
class Mas_Mascnotes_Block_Notes extends Mage_Core_Block_Template {
	/**
	 * Set grid params
	 * @access protected
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();        
	}
	/**
	 * prepare the note collection
	 * @access protected 
	 * @return Mas_Mascnotes_Block_Adminhtml_Catalog_Product_Edit_Tab_Note
	 * 
	 */
	public function getCollection() {
		$collection = Mage::getResourceModel('mascnotes/note_collection');
		$collection->addFieldToFilter('status', 1);
		
		if ($this->getCustomerId()){
			$constraint = 'related.customer_id='.$this->getCustomerId();
			}
			else{
				$constraint = 'related.customer_id=0';
			}
		$collection->getSelect()->joinRight(
			array('related'=>$collection->getTable('mascnotes/note_customer')),
			'related.note_id=main_table.entity_id AND '.$constraint,
			array('position')
		);
		$collection->getSelect()->joinLeft(
			array('user'=>$collection->getTable('admin/user')),
			'user.user_id=main_table.user_id',
			array('firstname', 'lastname')
		);
		return $collection;		
	}
	
	public function getCustomerId(){
		return Mage::getSingleton('customer/session')->getCustomer()->getId();
	}
	
}
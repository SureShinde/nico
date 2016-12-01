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
 * Adminhtml observer
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Model_Adminhtml_Observer{
	/**
	 * check if tab can be added
	 * @access protected
	 * @param Mage_Catalog_Model_Product $product
	 * @return bool
	 * 
	 */
	protected function _canAddTab($product){
		if ($product->getId()){
			return true;
		}
		return false;
	}
	/**
	 * add the note tab to products
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return Mas_Mascnotes_Model_Adminhtml_Observer
	 * 
	 */
	public function addNoteBlock($observer){
		$block = $observer->getEvent()->getBlock();
		
        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs){
			$id = Mage::registry('current_customer')->getId();
			if ($id) {
					$block->addTab('customer_notes', array(
                        'label' => Mage::helper('mascnotes')->__('Notes On Customer'),
                        'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/mascnotes_note_customer/notes', array('_current' => true, 'customer_id' => $id)),
                    'class' => 'ajax',
                ));
			}
		}
		
		if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tabs){
			$id = Mage::registry('current_order');
			if ($id && $id->getCustomerId() != null) {
				$block->addTabAfter('customer_notes', array(
                	'label' => Mage::helper('mascnotes')->__('Notes On Customer'),
                    'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/mascnotes_note_customer/notes', array('customer_id' => $id->getCustomerId(), 'is_order' => 1)),
					'class' => 'ajax',
                ), 'order_transactions');
			}
		}

		return $this;
	}
	/**
	 * save note - product relation
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return Mas_Mascnotes_Model_Adminhtml_Observer
	 * 
	 */
	public function saveNoteData($observer){
		
		$product = Mage::registry('current_customer');
		
		/* @var $model Mas_Mascnotes_Model_Resource_Note_Customer */
		$model = Mage::getResourceSingleton('mascnotes/note_customer');
		
		/* @var $model Mas_Mascnotes_Model_Resource_Note */
		$noteModel = Mage::getResourceSingleton('mascnotes/note');
		
		$new = Mage::app()->getRequest()->getParam('mascnotes_new', array());
		
		$userId = Mage::getSingleton('admin/session')->getUser()->getUserId();
		$add = array();
		foreach ($new as $note) {
			if ($note['note'] != '') {
				$note['user_id'] = $userId;
				$add[] = $note;
			}
		}
		
		/*
		 * Add Note
		 */
		$data = array();
		foreach ($add as $note) {
			$newNote = Mage::getModel('mascnotes/note');
			$newNote
				->addData($note)
				->setStores($product->getStoreId())
				->save();
			$data[] = array(
				'customer_id' => $product->getId(),
				'note_id' => $newNote->getId(),
				'position' => 0,
			);
		}
		$model->saveSingleProductRelation($data);
		
		/*
		 * Update Existing
		 */
		$existing = Mage::app()->getRequest()->getParam('mascnotes_note', array());
		foreach ($existing as $noteId => $note) {
			if (isset($note['delete'])) {
				$model->deleteCustomerRelation($noteId, $product->getId());
				continue;
			}
			$found = Mage::getModel('mascnotes/note')->load($noteId);
			if ($found) {
				if ($found->getNote() != $note['note'] || $found->getStatus() != $note['status']) {
					$found->addData($note)->save();
				}	
			}
		}
		return $this;
	}}
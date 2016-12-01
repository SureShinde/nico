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
 * Note model
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Model_Note extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'mascnotes_note';
	const CACHE_TAG = 'mascnotes_note';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'mascnotes_note';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'note';
	protected $_productInstance = null;
	/**
	 * constructor
	 * @access public
	 * @return void
	 * 
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('mascnotes/note');
	}
	/**
	 * before save note
	 * @access protected
	 * @return Mas_Mascnotes_Model_Note
	 * 
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		} else {
            $this->setUpdatedAt($now);
		}
		return $this;
	}
	/**
	 * save note relation
	 * @access public
	 * @return Mas_Mascnotes_Model_Note
	 * 
	 */
	protected function _afterSave() {
		$this->getProductInstance()->saveNoteRelation($this);
		return parent::_afterSave();
	}
	/**
	 * get product relation model
	 * @access public
	 * @return Mas_Mascnotes_Model_Note_Customer
	 * 
	 */
	public function getProductInstance(){
		if (!$this->_productInstance) {
			$this->_productInstance = Mage::getSingleton('mascnotes/note_customer');
		}
		return $this->_productInstance;
	}
	/**
	 * get selected products array
	 * @access public
	 * @return array
	 * 
	 */
	public function getSelectedProducts(){
		if (!$this->hasSelectedProducts()) {
			$products = array();
			foreach ($this->getSelectedProductsCollection() as $product) {
				$products[$product->getEntityId()] = $product;
			}
			$this->setSelectedProducts($products);
		}
		return $this->getData('selected_products');
	}
	/**
	 * Retrieve collection selected products
	 * @access public
	 * @return Mas_Mascnotes_Resource_Note_Customer_Collection
	 * 
	 */
	public function getSelectedProductsCollection(){
		$collection = $this->getProductInstance()->getProductCollection($this);
		return $collection;
	}
}
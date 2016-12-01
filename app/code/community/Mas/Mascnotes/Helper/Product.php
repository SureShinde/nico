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
 * Product helper
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Helper_Product extends Mas_Mascnotes_Helper_Data{
	/**
	 * get the selected notes for a product
	 * @access public
	 * @param Mage_Catalog_Model_Product $product
	 * @return array()
	 * 
	 */
	public function getSelectedNotes(Mage_Catalog_Model_Product $product){
		if (!$product->hasSelectedNotes()) {
			$notes = array();
			foreach ($this->getSelectedNotesCollection($product) as $note) {
				$notes[] = $note;
			}
			$product->setSelectedNotes($notes);
		}
		return $product->getData('selected_notes');
	}
	/**
	 * get note collection for a product
	 * @access public
	 * @param Mage_Catalog_Model_Product $product
	 * @return Mas_Mascnotes_Model_Resource_Note_Collection
	 */
	public function getSelectedNotesCollection(Mage_Catalog_Model_Product $product){
		$collection = Mage::getResourceSingleton('mascnotes/note_collection')
			->addProductFilter($product);
		return $collection;
	}
}
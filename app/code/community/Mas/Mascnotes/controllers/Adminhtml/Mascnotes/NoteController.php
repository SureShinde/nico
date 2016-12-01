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
 * Note admin controller
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Adminhtml_Mascnotes_NoteController extends Mas_Mascnotes_Controller_Adminhtml_Mascnotes{
	/**
	 * init the note
	 * @access protected
	 * @return Mas_Mascnotes_Model_Note
	 */
	protected function _initNote(){
		$noteId  = (int) $this->getRequest()->getParam('id');
		$note	= Mage::getModel('mascnotes/note');
		if ($noteId) {
			$note->load($noteId);
		}
		Mage::register('current_note', $note);
		return $note;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * 
	 */
     protected function _isAllowed() {
       return true;
    }
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('mascnotes')->__('Notes on Customers'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void
	 * 
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit note - action
	 * @access public
	 * @return void
	 * 
	 */
	public function editAction() {
		$noteId	= $this->getRequest()->getParam('id');
		$note  	= $this->_initNote();
		if ($noteId && !$note->getId()) {
			$this->_getSession()->addError(Mage::helper('mascnotes')->__('This note no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$note->setData($data);
		}
		Mage::register('note_data', $note);
		$this->loadLayout();
		$this->_title(Mage::helper('mascnotes')->__('Notes On Customers'));
		
		if ($note->getId()){
			$this->_title(strip_tags($note->getNote()));
		}
		else{
			$this->_title(Mage::helper('mascnotes')->__('Add note'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new note action
	 * @access public
	 * @return void
	 * 
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save note - action
	 * @access public
	 * @return void
	 * 
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('note')) {
			try {
				$note = $this->_initNote();
				$note->addData($data);
				$products = $this->getRequest()->getPost('customers', -1);
				if ($products != -1) {
					$note->setProductsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($products));
				}
				$note->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mascnotes')->__('Note was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $note->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('There was a problem saving the note.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('Unable to find note to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete note - action
	 * @access public
	 * @return void
	 * 
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$note = Mage::getModel('mascnotes/note');
				$note->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mascnotes')->__('Note was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('There was an error deleteing note.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('Could not find note to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete note - action
	 * @access public
	 * @return void
	 * 
	 */
	public function massDeleteAction() {
		$noteIds = $this->getRequest()->getParam('note');
		if(!is_array($noteIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('Please select notes to delete.'));
		}
		else {
			try {
				foreach ($noteIds as $noteId) {
					$note = Mage::getModel('mascnotes/note');
					$note->setId($noteId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mascnotes')->__('Total of %d notes were successfully deleted.', count($noteIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('There was an error deleteing notes.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass status change - action
	 * @access public
	 * @return void
	 * 
	 */
	public function massStatusAction(){
		$noteIds = $this->getRequest()->getParam('note');
		if(!is_array($noteIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('Please select notes.'));
		} 
		else {
			try {
				foreach ($noteIds as $noteId) {
				$note = Mage::getSingleton('mascnotes/note')->load($noteId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d notes were successfully updated.', count($noteIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mascnotes')->__('There was an error updating notes.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * get grid of products action
	 * @access public
	 * @return void
	 * 
	 */
	public function customersAction(){
		$this->_initNote();
		$this->loadLayout();
		$this->getLayout()->getBlock('note.edit.tab.customer')
			->setNoteCustomers($this->getRequest()->getPost('note_customers', null));
		$this->renderLayout();
	}
	/**
	 * get grid of products action
	 * @access public
	 * @return void
	 * 
	 */
	public function customersgridAction(){
		$this->_initNote();
		$this->loadLayout();
		$this->getLayout()->getBlock('note.edit.tab.customer')
			->setNoteCustomers($this->getRequest()->getPost('note_customers', null));
		$this->renderLayout();
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void
	 * 
	 */
	public function exportCsvAction(){
		$fileName   = 'note.csv';
		$content	= $this->getLayout()->createBlock('mascnotes/adminhtml_note_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * 
	 */
	public function exportExcelAction(){
		$fileName   = 'note.xls';
		$content	= $this->getLayout()->createBlock('mascnotes/adminhtml_note_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * 
	 */
	public function exportXmlAction(){
		$fileName   = 'note.xml';
		$content	= $this->getLayout()->createBlock('mascnotes/adminhtml_note_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}
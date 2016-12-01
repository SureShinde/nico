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
 * Note edit form tab
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Mascnotes_Note_Block_Adminhtml_Note_Edit_Tab_Form
	 * 
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('note_');
		$form->setFieldNameSuffix('note');
		$this->setForm($form);
		$fieldset = $form->addFieldset('note_form', array('legend'=>Mage::helper('mascnotes')->__('Note'), 'class' => 'fieldset-wide'));
		
		if (Mage::getStoreConfig('mascnotes/general/editor')) {
			
			$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
			$fieldset->addField('note', 'editor', array(
				'label' => Mage::helper('mascnotes')->__('Note'),
				'name'  => 'note',
				'required'  => true,
				'config' => $wysiwygConfig,
				'class' => 'required-entry',
				'style' => 'height: 100px;'
			));
			
		} else {
			$fieldset->addField('note', 'textarea', array(
				'label' => Mage::helper('mascnotes')->__('Note'),
				'name'  => 'note',
				'required'  => true,
				'class' => 'required-entry',
				'style' => 'height: 100px;'
			));
		}

		$fieldset->addField('user_id', 'select', array(
			'label' => Mage::helper('mascnotes')->__('Posted By'),
			'name'  => 'user_id',
			'values' => Mage::helper('mascnotes')->getAdmins()

		));
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('mascnotes')->__('Visible To Customer'),
			'name'  => 'status',
			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('mascnotes')->__('Yes'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('mascnotes')->__('No'),
				),
			),
		));

		if (Mage::getSingleton('adminhtml/session')->getNoteData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getNoteData());
			Mage::getSingleton('adminhtml/session')->setNoteData(null);
		}
		elseif (Mage::registry('current_note')){
			$form->setValues(Mage::registry('current_note')->getData());
		}
		return parent::_prepareForm();
	}
}
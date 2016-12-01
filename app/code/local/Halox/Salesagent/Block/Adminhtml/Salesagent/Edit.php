<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2014
 */
/**
 * Agent admin edit form
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
          */
    public function __construct(){
        parent::__construct(); 
        $this->_blockGroup = 'halox_salesagent';
        $this->_controller = 'adminhtml_salesagent';
        $this->_updateButton('save', 'label', 'Save Agent');
        $this->_updateButton('delete', 'label', 'Delete Agent');
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('halox_salesagent')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		 $isDelAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Delete');
         if(!$isDelAllowed){
         $this->_removeButton('delete');
         }
         $isBackAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Back');
        
         if(!$isBackAllowed){
         $this->_removeButton('back');
         }
         $isResetAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Reset');
         if(!$isResetAllowed){
         $this->_removeButton('reset');
         }
         $isEditAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Edit');
         if(!$isEditAllowed){
         $this->_removeButton('save');
         $this->_removeButton('saveandcontinue');
         }
    }
    /**
     * get the edit form header
     * @access public
     * @return string
          */
    public function getHeaderText(){
        if( Mage::registry('current_agent') && Mage::registry('current_agent')->getId() ) {
            return "Edit Agent";
        }
        else {
            return 'Add Agent';
        }
    }
}

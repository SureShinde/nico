<?php
/**
 * Magebird.com
 *
 * @category   Magebird
 * @package    Magebird_Popup
 * @copyright  Copyright (c) 2014 Magebird (http://www.Magebird.com)
 * @license    http://www.magebird.com/licence
 * Any form of ditribution, sell, transfer forbidden see licence above 
 */
class Magebird_Popup_Block_Adminhtml_Popup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('popup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('popup')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('popup')->__('General Information'),
          'title'     => Mage::helper('popup')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_form')->toHtml(),
      ));
      
	    $this->addTab('appearance', array(
          'label'     => Mage::helper('popup')->__('Appearance & css'),
          'title'     => Mage::helper('popup')->__('Appearance & css'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_appearance')->toHtml(),
      ));
      
	    $this->addTab('position', array(
          'label'     => Mage::helper('popup')->__('Position'),
          'title'     => Mage::helper('popup')->__('Position'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_position')->toHtml(),
      ));              
      
	    $this->addTab('settings', array(
          'label'     => Mage::helper('popup')->__('Settings'),
          'title'     => Mage::helper('popup')->__('Settings'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_settings')->toHtml(),
      ));   
      
	    $this->addTab('conditions', array(
          'label'     => Mage::helper('popup')->__('Extra show conditions'),
          'title'     => Mage::helper('popup')->__('Extra show conditions'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_conditions')->toHtml(),
      ));    
      /*
	    $this->addTab('customfields', array(
          'label'     => Mage::helper('popup')->__('Custom fields'),
          'title'     => Mage::helper('popup')->__('Custom fields'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_customfields')->toHtml(),
      ));   
      */          

      return parent::_beforeToHtml();
  }
}
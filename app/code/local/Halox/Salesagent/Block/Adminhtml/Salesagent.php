<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * SalesAgent admin block
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent
    extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * constructor
     * @access public
     * @return void
     */
    public function __construct(){
        $this->_controller         = 'adminhtml_salesagent';
        $this->_blockGroup         = 'halox_salesagent';
        parent::__construct();
        $this->_headerText         = Mage::helper('halox_salesagent')->__('Agent');
        $this->_updateButton('add', 'label', Mage::helper('halox_salesagent')->__('Add Agent'));
		$isAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Add');
        if(!$isAllowed){
        $this->_removeButton('add');
        }

    }
}

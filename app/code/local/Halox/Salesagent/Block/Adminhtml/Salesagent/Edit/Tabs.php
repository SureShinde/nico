<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Salesagent admin edit tabs
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
          */
    public function __construct() { 
        parent::__construct();
        $this->setId('salesagent');
        $this->setDestElementId('edit_form');
        $this->setTitle('Sales Agent');
    }
    /**
     * before render html
     * @access protected
     * @return Halox_Salesagent_Block_Adminhtml_Adbanner_Edit_Tabs
          */
    protected function _beforeToHtml(){
        $this->addTab('form_salesagent', array(
            'label'        => 'Sales Agent',
            'title'        => 'Sales Agent',
            'content'     => $this->getLayout()->createBlock('halox_salesagent/adminhtml_salesagent_edit_tab_form')->toHtml(),
        ));
		$this->addTab('form_salesagent1', array(
            'label'        => 'Messages',
            'title'        => 'Messages',
            'content'     =>  $this->getLayout()->createBlock('halox_salesagent/adminhtml_salesagent_edit_tab_messages')->setTemplate('salesagent/messages.phtml')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve Agent entity
     * @access public
     * @return Halox_Salesagent_Model_Salesagent
          */
    public function getAgent(){
        return Mage::registry('current_agent');
    }
}

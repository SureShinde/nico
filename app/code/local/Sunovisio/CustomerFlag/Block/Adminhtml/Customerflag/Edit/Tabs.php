<?php

class Sunovisio_CustomerFlag_Block_Adminhtml_Customerflag_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('customerflag_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customerflag')->__('Item Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('customerflag')->__('Item Information'),
            'title' => Mage::helper('customerflag')->__('Item Information'),
            'content' => $this->getLayout()->createBlock('customerflag/adminhtml_customerflag_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
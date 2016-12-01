<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('ticket_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('helpdesk')->__('Client Information'));
    }

    protected function _beforeToHtml() {
        $viewTabFormBlock = $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tab_form');
        $viewTabFormBlock->setTemplate('mw_helpdesk/widget/form.phtml');
        $this->addTab('form_section', array(
            'label' => Mage::helper('helpdesk')->__('Client Information'),
            'title' => Mage::helper('helpdesk')->__('Client Information'),
            'content' => $viewTabFormBlock->toHtml(),
        ));

        $this->addTab('history', array(
            'label' => Mage::helper('helpdesk')->__('Ticket Thread'),
            'title' => Mage::helper('helpdesk')->__('Ticket Thread'),
            'content' => $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tab_history')->toHtml(),
            'active' => true
        ));

        $this->addTab('information', array(
            'label' => Mage::helper('helpdesk')->__('Thread Links'),
            'title' => Mage::helper('helpdesk')->__('Thread Links'),
            'content' => $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tab_information')->toHtml(),
        ));
        
        $this->addTab('note_information', array(
            'label' => Mage::helper('helpdesk')->__('Internal Note'),
            'title' => Mage::helper('helpdesk')->__('Internal Note'),
            'content' => $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tab_note')->toHtml(),
        ));

		$this->addTab('shareinfo', array(
            'label' => Mage::helper('helpdesk')->__('Customer Information'),
            'title' => Mage::helper('helpdesk')->__('Customer Information'),
            'content' => $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_view_tab_shareinfo')->toHtml(),
        ));
		
        if (Mage::registry('current_order')) {
            $orderedItemsBlock = $this->getLayout()->createBlock('helpdesk/adminhtml_sales_order_view_items');
            $orderedItemsBlock->setTemplate('mw_helpdesk/sales/order/view/items.phtml');
            $orderedItemsBlock->addItemRender('default', 'adminhtml/sales_order_view_items_renderer_default', 'mw_helpdesk/sales/order/view/items/renderer/default.phtml');
            
            $orderedItemsBlock->addColumnRender('name', 'adminhtml/sales_items_column_name', 'sales/items/column/name.phtml');
            //$orderedItemsBlock->addColumnRender('name', 'adminhtml/sales_items_column_name_grouped', 'sales/items/column/name.phtml', 'grouped');

            $this->addTab('ordered_item', array(
                'label' => Mage::helper('helpdesk')->__('Ordered Items'),
                'title' => Mage::helper('helpdesk')->__('Ordered Items'),
                'content' => $orderedItemsBlock->toHtml(),
            ));
        }

        if (Mage::getModel('core/session')->hasTicketId()) {
            $this->addTab('ticket_history', array(
                'label' => Mage::helper('helpdesk')->__('Ticket History'),
                'title' => Mage::helper('helpdesk')->__('Ticket History'),
                'content' => $this->getLayout()->createBlock('helpdesk/adminhtml_customer_ticket_grid')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

}
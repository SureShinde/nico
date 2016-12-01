<?php

class MW_HelpDesk_Block_Adminhtml_Ticket extends Mage_Adminhtml_Block_Widget_Container
{
	
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_helpdesk/ticket.phtml');
    }
    
    /**
     * Prepare button and grid
     *
     * @return MW_Helpdesk_Block_Adminhtml_Ticket
     */
    protected function _prepareLayout()
    {
        $this->_addButton('add_new', array(
            'label'   => Mage::helper('helpdesk')->__('Add Ticket'),
            //'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'onclick' => "setLocation('{$this->getUrl('*/*/new/action/'.Mage::app()->getRequest()->getActionName())}')",
            'class'   => 'add'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid', 'ticket.grid'));
        //$this->setChild('notice', $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_notice'));
        $this->setChild('message', $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_notification_message'));
        return parent::_prepareLayout();
    }
    
//    public function getAddButtonHtml()
//    {
//        return $this->getLayout()
//            ->createBlock('adminhtml/widget_button')
//            ->setData(array(
//            	'id'    => 'notice_grid_button',
//            	'label' => $this->__('View Notify Ticket'),
//        	))
//            ->toHtml();
//    }
    
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
    
}
<?php

class MW_HelpDesk_Block_Adminhtml_Ticket_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct(); 
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpdesk';		// module name
      //  $this->_controller = 'hdadmin_ticket';
        $this->_controller = 'adminhtml_ticket';
        $this->_mode = 'view';
        
        $this->_updateButton('save', 'label', Mage::helper('helpdesk')->__('Save Ticket'));
//      $this->_removeButton('delete');
        $this->updateButton('back','onclick','setLocation(\''.$this->getUrl('*/*/'.$this->getRequest()->getParam('action')).'\')');
        $this->addButton('spam', array(
            'label' => Mage::helper('helpdesk')->__('Mark as spam'),
            'onclick' => 'setLocation(\''.$this->getUrl('*/adminhtml_spam/markSpam', array('id' => Mage::getModel('core/session')->getTicketId())).'\')',
            'class' => 'button'
            ));
		
		$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/view/');
            }
        ";
    }
	
   	public function getHeaderCssClass() 
   	{
        return 'icon-head head-customer-groups';
    }
    
    public function getHeaderText()
    {
        if( Mage::registry('ticket_data') && Mage::registry('ticket_data')->getId() ) {
            return Mage::helper('helpdesk')->__(Mage::registry('ticket_data')->getCodeId() . ' - ' . $this->htmlEscape(Mage::registry('ticket_data')->getSubject()));
        }
    }
}
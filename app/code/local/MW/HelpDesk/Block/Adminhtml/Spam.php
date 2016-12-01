<?php
class MW_HelpDesk_Block_Adminhtml_Spam extends Mage_Adminhtml_Block_Widget_Grid_Container
{   
    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'helpdesk';
        $this->_controller = 'adminhtml_spam';
        $this->_headerText = Mage::helper('helpdesk')->__('Manage Spam');
        $this->removeButton('add');
    }
    
}
?>

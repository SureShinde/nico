<?php
class MW_HelpDesk_Block_Ticketcontainer extends Mage_Core_Block_Template{
    public function _construct() {
        parent::_construct();
        $this->setTemplate('mw_helpdesk/ticketcontainer.phtml');
    }
}
?>

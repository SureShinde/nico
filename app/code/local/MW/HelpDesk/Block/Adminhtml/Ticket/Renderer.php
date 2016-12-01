<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_Renderer
extends Mage_Adminhtml_Block_Abstract implements 
Varien_Data_Form_Element_Renderer_Interface {
  public function render(Varien_Data_Form_Element_Abstract $element) {
    //You can write html for your button here
    $html = '<button></button>';
   	return $html;
  }
} 

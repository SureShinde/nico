<?php

class Halox_AgeVerification_Block_Adminhtml_Order_Renderer_Verificationstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
    	$value = $row->getData($this->getColumn()->getIndex());
    	
    	$value = ($value == 'Age Verification Pending') 
    		? '<span style="color:red"><strong>' . $value . '</strong></span>'
    		: '<span style="color:green"><strong>' . $value . '</strong></span>';
        
        return $value ? $value : '--';
    }

}
?>
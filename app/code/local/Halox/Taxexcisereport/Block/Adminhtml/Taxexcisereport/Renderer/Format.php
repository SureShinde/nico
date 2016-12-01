<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Format extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    public function render(Varien_Object $row) {
	 $val = $row->getData($this->getColumn()->getIndex());
	 return round($val,2);
    }

}

?>
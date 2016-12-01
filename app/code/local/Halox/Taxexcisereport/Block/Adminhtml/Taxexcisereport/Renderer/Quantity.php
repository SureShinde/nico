<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Quantity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $productId = $row->getData($this->getColumn()->getIndex());
        if (isset($productId) && !empty($productId)) {
                $quantity = (int) $row ->getdata('qty_ordered');
				return $quantity;
            }
       

        return "-";
    }

}

?>
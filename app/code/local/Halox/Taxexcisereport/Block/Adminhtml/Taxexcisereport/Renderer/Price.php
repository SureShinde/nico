<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $productId = $row->getData($this->getColumn()->getIndex());
        if (isset($productId) && !empty($productId)) {
           if (isset($productId) && !empty($productId)) {
                $price = $row ->getdata('price');
				$productPrice = number_format((float)$price, 2, '.', '');
				return $productPrice;
            }
        }

        return "-";
    }

}

?>
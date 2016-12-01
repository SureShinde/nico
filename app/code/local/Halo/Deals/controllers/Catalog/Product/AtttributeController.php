<?php
include_once("Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php");
class Halo_Deals_Catalog_Product_AttributeController extends Mage_Adminhtml_Catalog_Product_AttributeController
{
     protected function _filterPostData($data)
    {
        if ($data) {
            /** @var $helperCatalog Mage_Catalog_Helper_Data */
            $helperCatalog = Mage::helper('catalog');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helperCatalog->stripTags($value);
                }
            }
        }
        return $data;
    }
}
<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Taxexcisereport extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public static $customColumns = false;
    public $file;
    
    public function __construct($file = null) {
        $this->file = $file;
     }
    
    public function render(Varien_Object $row) {
        if (empty(self::$customColumns) && ($this->file == false)) {
            self::$customColumns = true;
            return $row->getData('eliquid_ml');
        }

        if($row->getData('increment_id') == 'Totals' && $this->file == true){
            return $row->getData('eliquid_ml');
        }
        
        
     // $productId = $row->getData($this->getColumn()->getIndex());
         $itemId = $row->getData($this->getColumn()->getIndex());
         $orderItem = Mage::getModel('sales/order_item')->load($itemId);
         $orderItemArr = $orderItem ->getData();
         
         if(isset($orderItemArr) && !empty($orderItemArr)){
             $productId = $orderItemArr['product_id'];
             $sku = $orderItemArr['sku'];
         }
         
       if($orderItemArr['product_type'] == 'configurable'){
         
        $_product = Mage::getModel('catalog/product')
                ->loadByAttribute('sku',$sku);
        
        $eMl = $_product->getAttributeText('eliquid_ml');
        if (isset($eMl) && !empty($eMl)) {
            return $eMl;
        }
        
       }else{
           
        $_product = Mage::getModel('catalog/product')
                ->load($productId);
        
        $eMl = $_product->getAttributeText('eliquid_ml');
        if (isset($eMl) && !empty($eMl)) {
            return $eMl;
        }
        
       }
        

        return "-";
    }

}

?>
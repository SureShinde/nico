<?php

class MyCustomAttributes extends Wyomind_Datafeedmanager_Model_Datafeedmanager{
    
  
   public function _eval($product,$exp,$value){
       
        // Example of custom attribute
        switch ($exp['pattern']) {

           
           case "{configurable_sizes}":
              
               if($product->type_id=='configurable'){ 
                    // Your custom script
                    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                    $sizes = array();
                    foreach ($childProducts as $child)
                        $sizes[]= $child->getAttributeText('size');

                    return implode(',',$sizes);
               }
               else return  false;

           break;
           
        
           
           
           
           /*************** DO NOT CHANGE THESE LINES ***************/
           default :
               return $value;
           break;
           /*************** DO NOT CHANGE THESE LINES ***************/
        }
    }
}
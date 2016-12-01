<?php
$installer = Mage::getResourceModel('customer/setup', 'customer_setup');
$installer->startSetup();
$installer->addAttribute('customer','minimum_order_amount', array( 
    'label'             => 'Minimum Order Amount',
    'type'              => 'text',    //backend_type
    'input'             => 'text', //frontend_input
    'backend'           => '',    
    'global'            =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source'            => '', // Goes to Step 2   
    'visible'                 => true,
    'required'                => false,
    'user_defined'            => true,
    'searchable'              => false,
    'filterable'              => false,
    'comparable'              => false,
    'visible_on_front'        => false,
    'unique'                  => false,
    'frontend_class'          => 'validate-number',
));


Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'minimum_order_amount')
   ->setStoreIds('1')
    ->save();
   
$installer->endSetup();
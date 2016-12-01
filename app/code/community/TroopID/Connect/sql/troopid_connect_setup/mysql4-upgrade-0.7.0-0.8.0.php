<?php
$installer = Mage::getResourceModel('customer/setup', 'customer_setup');
$installer->startSetup();
$installer->addAttribute('customer','troopid_access_token', array( 
    'label'             => 'Troop Id Access Token',
    'type'              => 'text',    //backend_type
    'input'             => 'textarea', //frontend_input
    'backend'           => '',    
    'global'            =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source'            => '',
    'visible'                 => true,
    'required'                => false,
    'user_defined'            => true,
    'searchable'              => false,
    'filterable'              => false,
    'comparable'              => false,
    'visible_on_front'        => false,
    'unique'                  => false
));
$installer->endSetup();
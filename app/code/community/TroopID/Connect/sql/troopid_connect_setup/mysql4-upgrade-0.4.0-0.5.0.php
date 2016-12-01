<?php
$installer = Mage::getResourceModel('customer/setup', 'customer_setup');
$installer->startSetup();
$installer->addAttribute('customer','troopid_affiliation', array( 
    'label'             => 'Troop Id Affiliation',
    'type'              => 'text',    //backend_type
    'input'             => 'text', //frontend_input
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
$installer->addAttribute('customer','troopid_uid', array( 
    'label'             => 'Troop Id Uid',
    'type'              => 'text',    //backend_type
    'input'             => 'text', //frontend_input
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
$installer->addAttribute('customer','troopid_scope', array( 
    'label'             => 'Troop Id Scope',
    'type'              => 'text',    //backend_type
    'input'             => 'text', //frontend_input
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
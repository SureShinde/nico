<?php
$installer = Mage::getResourceModel('customer/setup', 'customer_setup');
$installer->startSetup();
$installer->addAttribute('customer','sales_rep', array( 
    'label'             => 'Sales Agent',
    'type'              => 'text',    //backend_type
    'input'             => 'select', //frontend_input
    'backend'           => '',    
    'global'            =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source'            => 'halox_salesagent/attribute_source_type', // Goes to Step 2   
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


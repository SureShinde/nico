<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
$installer = Mage::getResourceModel('customer/setup', 'customer_setup');
$installer->startSetup();
$installer->addAttribute('customer','additional_wire_transfer_fee', array( 
    'label'             => 'Additional Wire Transfer Fee',
    'type'              => 'text',    //backend_type
    'input'             => 'text', //frontend_input
    'backend'           => '',    
    'global'            =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source'            => "", 
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false
));

$installer->endSetup();
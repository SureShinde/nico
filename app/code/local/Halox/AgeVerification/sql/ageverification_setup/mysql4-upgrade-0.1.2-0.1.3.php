<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('customer', 'customer_age', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Age',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source' => '',
));


$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_age');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->endSetup();

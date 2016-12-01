<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('customer', 'isVerified', array(
    'input' => 'select',
    'type' => 'int',
    'label' => 'Is Age Verified',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'source'     => 'eav/entity_attribute_source_boolean',
    'option' => array('values' => array('No', 'Yes')),
));


$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'isVerified');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$setup->endSetup();

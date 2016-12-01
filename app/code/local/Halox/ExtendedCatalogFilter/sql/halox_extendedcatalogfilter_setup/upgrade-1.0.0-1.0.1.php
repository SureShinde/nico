<?php 

/**
 * Install 'Show Extended Catalog' attribute to customer_customer entity
 */

$installer = Mage::getResourceModel('customer/setup', 'customer_setup');

$installer->startSetup();

$installer->addAttribute('customer', Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CUSTOMER_ENTITY, array(
  'type'              => 'int',
  'backend'           => '',
  'frontend'          => '',
  'label'             => 'Show Extended Catalog',
  'input'             => 'select',
  'class'             => '',
  'source'            => 'eav/entity_attribute_source_boolean',
  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
  'visible'           => true,
  'required'          => false,
  'user_defined'      => false,
  'default'           => '',
  'searchable'        => false,
  'filterable'        => false,
  'comparable'        => false,
  'visible_on_front'  => false,
  'unique'            => false,
  'sort_order'        => 105,
  'position'          => 105,
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CUSTOMER_ENTITY);
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->save();


$installer->endSetup();
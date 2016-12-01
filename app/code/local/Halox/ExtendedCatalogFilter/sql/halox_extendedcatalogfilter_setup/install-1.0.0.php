<?php 

/**
 * Install 'Is Extended Catalog Product' attribute to catalog_product entity
 */

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->startSetup();

$installer->addAttribute('catalog_product', Halox_ExtendedCatalogFilter_Helper_Data::ATTRIBUTE_CODE_CATALOG_PRODUCT_ENTITY, array(
  'type'              => 'int',
  'backend'           => '',
  'frontend'          => '',
  'label'             => 'Is Extended Catalog Product',
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
  'group'             => 'General',
  'used_in_product_listing' => 1,
  'visible_on_front' => 1
));

$installer->endSetup();
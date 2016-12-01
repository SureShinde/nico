<?php
/**
 * Halox_Fixes module
 *
 * @category    fixes
 * @package     Halox_Fixes
 */


$installer = $this;
$installer->startSetup();
 
$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);

$attributeGroupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection');
$attributeGroupCollection->addFieldToFilter('attribute_set_id', $attributeSetId);
$attributeGroupCollection->addFieldToFilter('attribute_group_name', 'General Information');
$attributeGroupCollection->setCurPage(1)->getPageSize(1);
$attributeGroupId = $attributeGroupCollection->getFirstItem()->getId();

$installer->addAttribute('catalog_category', 'og_description',  array(
    'type'     => 'text',
    'label'    => 'OG Description',
    'input'    => 'textarea',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
));

$installer->addAttribute('catalog_category', 'tc_description',  array(
    'type'     => 'text',
    'label'    => 'TC Description',
    'input'    => 'textarea',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
));
 
 
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'og_description',
    '8.5'
);

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'tc_description',
    '8.5'
);
 
$installer->endSetup();
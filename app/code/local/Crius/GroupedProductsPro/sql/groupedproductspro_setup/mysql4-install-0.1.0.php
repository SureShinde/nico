<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_GroupedProductsPro
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
 
$installer = $this;

$helper = Mage::helper('groupedproductspro');

$installer->startSetup();

// Create attribute field to toggle the attribute visibility in the grouped product table
$table = $installer->getTable('catalog/eav_attribute');
$installer->getConnection()->addColumn(
    $table,
    'is_visible_in_grouped_table',
    "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'"
);
$installer->getConnection()->addKey(
	$table,
	'IDX_VISIBLE_IN_GROUPED_TABLE',
	'is_visible_in_grouped_table'
);

// Create attribute field for sort order in the grouped product table
$installer->getConnection()->addColumn(
    $table,
    'grouped_table_sort_order',
    "INT( 11 ) UNSIGNED NOT NULL DEFAULT '0'"
);

// Add an attribute group for the grouped table settings
$defaultAttributeSetId = $this->getDefaultAttributeSetId('catalog_product');
$installer->addAttributeGroup('catalog_product', $defaultAttributeSetId, 'Table Settings');

// Add product attributes to allow overriding the configuration
$productConfigAttributes = array(
	array('code' => 'gpp_add_row_to_cart', 'label' => $helper->__('Add to Cart Buttons for Each Row')),
	array('code' => 'gpp_add_all_to_cart', 'label' => $helper->__('Add All to Cart Button in Table Footer')),
	array('code' => 'gpp_show_quantity', 'label' => $helper->__('Quantity Fields')),
);

foreach ($productConfigAttributes as $attributeData) {
	$installer->addAttribute('catalog_product', $attributeData['code'], array(
	    'group'             => 'Table Settings',
	    'type'              => 'int',
	    'backend'           => '',
	    'frontend'          => '',
	    'label'             => $attributeData['label'],
	    'input'             => 'select',
	    'class'             => '',
	    'source'            => 'groupedproductspro/product_attribute_source_enabledisableconfig',
	    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	    'visible'           => true,
	    'required'          => false,
	    'user_defined'      => true,
	    'default'           => '',
	    'searchable'        => false,
	    'filterable'        => false,
	    'comparable'        => false,
	    'visible_on_front'  => false,
	    'unique'            => false,
	    'apply_to'          => 'grouped',
	    'is_configurable'   => false,
	));
}

$installer->endSetup();
<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.9.2
 * @license:     QvHpbEc0oOzK3qo2hlmyDcWVqjqnkaNA9m9UogV4wV
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();

$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->updateAttribute('catalog_product', 'created_by', 'is_visible', '1'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'source_model', 'Aitoc_Aitpermissions_Model_Source_Admins'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_label', 'Product owner'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_input', 'select'); 

$installer->endSetup();
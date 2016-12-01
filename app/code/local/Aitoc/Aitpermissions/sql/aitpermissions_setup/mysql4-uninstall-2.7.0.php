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

$catalogSetup->updateAttribute('catalog_product', 'created_by', 'is_visible', '0'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'source_model', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_label', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_input', ''); 

$installer->endSetup();
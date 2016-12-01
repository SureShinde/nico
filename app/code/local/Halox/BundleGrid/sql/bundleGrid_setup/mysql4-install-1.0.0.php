<?php
/**
 * Halox_BundleGrid module
 *
 * @category    bundle grid
 * @package     Halox_BundleGrid
 */

$installer = $this;

$installer->startSetup();

$installer->run('
  ALTER TABLE  `catalog_product_bundle_option` ADD  `sub_product_description` text NULL;
');

$installer->endSetup();

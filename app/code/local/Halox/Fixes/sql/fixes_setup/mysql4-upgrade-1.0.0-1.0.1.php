<?php
/**
 * Halox_Fixes module
 *
 * @category    fixes
 * @package     Halox_Fixes
 */

$installer = $this;
$installer->startSetup();

$installer->run('
  ALTER TABLE  `cms_page` ADD  `og_description` text NULL, ADD  `tc_description` text NULL;
');

$installer->endSetup();
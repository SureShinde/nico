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
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run($sql = '
ALTER TABLE `' . $this->getTable('aitoc_aitpermissions_advancedrole') . '` ADD COLUMN `can_edit_global_attr` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;
');

$installer->endSetup();
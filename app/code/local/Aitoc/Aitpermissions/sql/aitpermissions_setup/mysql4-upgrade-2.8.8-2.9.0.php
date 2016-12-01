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

$installer->updateAttribute('catalog_product', 'created_by', 'source_model', 'Aitoc_Aitpermissions_Model_Source_Admins'); 


$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_editor_attribute')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned  NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `is_allow` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FC_AITOC_AITPERMISSIONS_EDITOR_ATTRIBUTE_ROLE_ID` FOREIGN KEY (`role_id`) REFERENCES {$this->getTable('admin/role')} (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `role_id` (`role_id`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
    
");


$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_editor_tab')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned  NOT NULL,
  `tab_code` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FC_AITOC_AITPERMISSIONS_EDITOR_TAB_ROLE_ID` FOREIGN KEY (`role_id`) REFERENCES {$this->getTable('admin/role')} (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
    
");

$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_editor_type')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned  NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FC_AITOC_AITPERMISSIONS_EDITOR_TYPE_ROLE_ID` FOREIGN KEY (`role_id`) REFERENCES {$this->getTable('admin/role')} (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD `can_create_products` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;
    
");
$installer->endSetup();
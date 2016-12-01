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
* @copyright  Copyright (c) 2012 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_approvedproducts')} (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `is_approved` smallint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
    
");

$installer->endSetup();
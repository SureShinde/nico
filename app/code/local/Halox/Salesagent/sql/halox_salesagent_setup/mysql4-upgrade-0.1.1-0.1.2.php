<?php
$installer = $this;

$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('halox_agents_message')}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(100) NOT NULL,
  `sales_agents_id` varchar(100) NOT NULL,
  `message` TEXT NOT NULL,
  `sent_by` varchar(30) NOT NULL  DEFAULT 'customer',
  `seen` int(5) NOT NULL,
  `status` int(5) NOT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


");

$installer->endSetup();

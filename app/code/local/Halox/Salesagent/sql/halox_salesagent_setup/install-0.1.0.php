<?php
$installer = $this;

$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('halox_sales_agents')}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `description` varchar(350) NOT NULL,
  `image` varchar(200) NOT NULL,
  `status` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


");

$installer->endSetup();

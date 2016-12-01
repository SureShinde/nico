<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('ageverification')};
CREATE TABLE {$this->getTable('ageverification')} (
  `ageverification_id` int(11) unsigned NOT NULL auto_increment,
  `country` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `pincode` varchar(255) NOT NULL default '',
  `age` varchar(255) NOT NULL default '',
  `region_id` int(11) unsigned NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`ageverification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
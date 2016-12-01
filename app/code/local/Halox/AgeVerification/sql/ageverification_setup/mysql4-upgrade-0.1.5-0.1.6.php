<?php

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('ageverification/ageverificationforstate')};
CREATE TABLE {$this->getTable('ageverification/ageverificationforstate')} (
  `ageverification_state_detail_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `country` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  PRIMARY KEY (`ageverification_state_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();

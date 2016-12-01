<?php

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('ageverification/ageverificationdetails')};
CREATE TABLE {$this->getTable('ageverification/ageverificationdetails')} (
  `customer_ageverification_details_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `ageverification_step` int(11) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `service` varchar(255) NOT NULL default '',
  `confirmation` varchar(255) NOT NULL default '',
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `zipcode` varchar(255) NOT NULL default '',
  `date_of_birth` varchar(255) NOT NULL default '',
  `ssn` int(11) unsigned,
  `verify_document` varchar(255) NOT NULL default '',
  `api_response` varchar(512) NOT NULL default '',
  PRIMARY KEY (`customer_ageverification_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();

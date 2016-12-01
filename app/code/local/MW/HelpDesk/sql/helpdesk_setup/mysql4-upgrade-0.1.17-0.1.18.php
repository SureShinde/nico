<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/shareinfo')};
CREATE TABLE {$resource->getTableName('helpdesk/shareinfo')} (
  `info_id` int(11) unsigned NOT NULL auto_increment,	
  `sender` varchar(255) NOT NULL,
  `share_info` text NOT NULL,
  PRIMARY KEY  (`info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
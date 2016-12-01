<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `tag_id` int(11) NOT NULL;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/tag')};
CREATE TABLE {$resource->getTableName('helpdesk/tag')} (
  `tag_id` int(11) unsigned NOT NULL auto_increment,	
  `ticket_id` int(11) unsigned NOT NULL, 
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
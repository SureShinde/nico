<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$conn = $installer->getConnection();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/ticketlog')};
CREATE TABLE {$resource->getTableName('helpdesk/ticketlog')} (
	  `id` int(11) unsigned NOT NULL auto_increment,	
	  `date_update` datetime NOT NULL,
	  `code_id` varchar(255) NOT NULL default '',
	  `customer_email`  varchar(255) NOT NULL default '',
	  `activity`  varchar(255) NOT NULL default '',
	  `staff_email`  varchar(255) NOT NULL default '',
	  `status` smallint(6) unsigned,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if (!$conn->tableColumnExists($resource->getTableName('helpdesk/gateway'), 'sender_name')) {
	$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/gateway')} ADD `sender_name` varchar(255) default NULL;
	");
}
$installer->endSetup();
<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/ruleticket')};
CREATE TABLE IF NOT EXISTS `{$resource->getTableName('helpdesk/ruleticket')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rule_id` int(11) NOT NULL default '0',
  `ticket_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


");
$installer->endSetup();
<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `is_created` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `is_updated` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `app_status` varchar(10) NOT NULL default '';

");
$installer->endSetup();
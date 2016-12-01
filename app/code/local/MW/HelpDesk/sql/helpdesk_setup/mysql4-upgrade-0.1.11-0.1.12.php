<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_status` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_priority` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_department_id` int(11) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_member_id` int(11) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_tags_name` text NOT NULL default '';
ALTER TABLE {$resource->getTableName('helpdesk/rules')} ADD `ac_template_id` int(11) unsigned default '0';
");
$installer->endSetup();
<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$sql ="ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `dcode` varchar(100) NOT NULL;";
$installer->run("

ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `step_reply_time` datetime;
ALTER TABLE {$resource->getTableName('helpdesk/history')} ADD `vote` tinyint(1) unsigned NOT NULL default '0';
ALTER TABLE {$resource->getTableName('helpdesk/member')} ADD `vote_like` int(11) unsigned NOT NULL default '0';
ALTER TABLE {$resource->getTableName('helpdesk/member')} ADD `vote_unlike` int(11) unsigned NOT NULL default '0';

");
$installer->endSetup();
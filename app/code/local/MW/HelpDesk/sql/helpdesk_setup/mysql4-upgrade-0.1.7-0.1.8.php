<?php

$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `time_to_process` int(11) NOT NULL default 0;
DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/spam')};
CREATE TABLE {$resource->getTableName('helpdesk/spam')} (
    `id` int(10) unsigned auto_increment,
    `email` varchar(50) NOT NULL,	
    PRIMARY KEY  (`id`),
    CONSTRAINT email_index UNIQUE INDEX (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
$installer->endSetup();

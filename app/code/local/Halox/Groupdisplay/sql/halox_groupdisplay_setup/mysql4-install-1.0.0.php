<?php
$installer = $this;
$installer->startSetup();
$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('halox_group_display_mapping')};
CREATE TABLE {$this->getTable('halox_group_display_mapping')} (
    `id` int(11) unsigned NOT NULL auto_increment,
    `group_id` int(11) unsigned NOT NULL,
    `attribute_set_id` int(11) unsigned NOT NULL,
    `show_on_frontend` tinyint(1) NOT NULL default '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();

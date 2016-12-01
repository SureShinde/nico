<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/template')} ADD `id_category` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `department_sort_order` tinyint(1) unsigned NOT NULL default '0';

");
$installer->endSetup();
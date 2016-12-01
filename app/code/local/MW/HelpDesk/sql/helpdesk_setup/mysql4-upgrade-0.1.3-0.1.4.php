<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `code_id` varchar(128) NOT NULL;
");
$installer->endSetup();
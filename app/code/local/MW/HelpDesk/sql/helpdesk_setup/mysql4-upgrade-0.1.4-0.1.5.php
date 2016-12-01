<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `note` text NOT NULL;
");
$installer->endSetup();
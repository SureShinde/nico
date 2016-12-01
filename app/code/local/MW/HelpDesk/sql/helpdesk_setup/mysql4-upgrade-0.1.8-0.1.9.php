<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `quicknote` text NOT NULL;
");
$installer->endSetup();
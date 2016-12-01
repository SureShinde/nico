<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$conn = $installer->getConnection();
if (!$conn->tableColumnExists($resource->getTableName('helpdesk/ticket'), 'store_view')) {
	$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `store_view` varchar(100) NOT NULL default '0';
	");
}
if (!$conn->tableColumnExists($resource->getTableName('helpdesk/ticket'), 'quicknote')) {
	$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `quicknote` text NOT NULL;
	");
}

$installer->endSetup();
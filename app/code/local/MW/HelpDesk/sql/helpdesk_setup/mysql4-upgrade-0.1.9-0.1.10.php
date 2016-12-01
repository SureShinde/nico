<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `item_id` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `buyer_username` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `email_ref_id` varchar(255) NOT NULL DEFAULT '';

");
$installer->endSetup();
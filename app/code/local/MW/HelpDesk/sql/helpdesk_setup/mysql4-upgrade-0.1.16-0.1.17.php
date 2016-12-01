<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `process_bar_staff` varchar(255) default NULL;

");
$installer->endSetup();
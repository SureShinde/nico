<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/ticket')} ADD `staff_working_time` datetime default NULL;

");
$installer->endSetup();
<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

ALTER TABLE {$resource->getTableName('helpdesk/history')} ADD `name` varchar(225) NOT NULL;

");
$installer->endSetup();
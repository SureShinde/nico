<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `verification_data` varchar(10240) NOT NULL DEFAULT ''");
$installer->endSetup();

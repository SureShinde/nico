<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `{$installer->getTable('sales/order')}` ADD `verification_status` varchar(255) NOT NULL DEFAULT ''");
$installer->endSetup();

<?php
$installer = $this;

$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('sales/quote')}`
  ADD   `payment_comment` varchar(400) NOT NULL
");
$installer->run("
ALTER TABLE `{$this->getTable('sales/order')}`
  ADD   `payment_comment` varchar(400) NOT NULL
");


$installer->endSetup();
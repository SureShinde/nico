<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amnotfound/log')}` ADD `request_path` varchar(255) NOT NULL AFTER `url`;
ALTER TABLE `{$this->getTable('amnotfound/log')}` ADD `status` TINYINT(1) NOT NULL AFTER `url`;
");

$this->endSetup(); 
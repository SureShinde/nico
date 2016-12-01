<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
$this->startSetup();

$this->run("ALTER TABLE `{$this->getTable('amnotfound/log')}` DROP COLUMN `status`;");

$this->endSetup();
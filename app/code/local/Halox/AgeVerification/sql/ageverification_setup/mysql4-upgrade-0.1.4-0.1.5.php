<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('ageverification/ageverificationdetails')} 
ADD COLUMN country varchar(255) NOT NULL default '' AFTER state

    ");

$installer->endSetup();

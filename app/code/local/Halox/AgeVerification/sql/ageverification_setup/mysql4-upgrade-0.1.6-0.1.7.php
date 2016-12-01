<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('ageverification/ageverificationdetails')} 
ADD COLUMN fail_message varchar(255) NOT NULL default '' AFTER verify_document

    ");

$installer->endSetup();

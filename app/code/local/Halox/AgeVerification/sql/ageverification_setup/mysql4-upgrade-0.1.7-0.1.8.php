<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('ageverification/ageverificationdetails')} 
ADD COLUMN quote_id int(11) NOT NULL default 0

    ");

$installer->endSetup();

<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  {$this->getTable('deals_gp_sl')}
    ADD COLUMN `sample_pack` varchar(255) NOT NULL
    AFTER `type_of_kit` ");

$installer->endSetup();

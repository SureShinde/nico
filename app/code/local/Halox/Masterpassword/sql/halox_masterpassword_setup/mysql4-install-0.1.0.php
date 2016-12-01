<?php

$installer = $this;

$installer->startSetup();

$installer->run(' ALTER TABLE `admin_user` ADD `masterpassword` VARCHAR( 255 ) NULL');

$installer->endSetup();

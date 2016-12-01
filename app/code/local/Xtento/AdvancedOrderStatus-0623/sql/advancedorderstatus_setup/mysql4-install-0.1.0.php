<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('advancedorderstatus/status_notification')} (
    `notification_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status_code` VARCHAR(100) NOT NULL,
    `store_id` INT(4) UNSIGNED NOT NULL,
    `template_id` INT(4) NOT NULL,
    UNIQUE (
        `status_code`,
        `store_id`
    ),
    PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
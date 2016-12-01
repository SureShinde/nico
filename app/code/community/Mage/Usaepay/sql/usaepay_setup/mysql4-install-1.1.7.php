<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// Create tables
$installer->run("

CREATE TABLE `{$this->getTable('usaepay_log')}` (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME NOT NULL,
    invoice VARCHAR(200) NOT NULL,
    card_holder VARCHAR(50),
    description VARCHAR(250),
    card_type VARCHAR(5) NOT NULL,
    card_number VARCHAR(100) NOT NULL,
    amount decimal(12,2) NOT NULL,    
    auth_code VARCHAR(30) NOT NULL,
    error_message VARCHAR(250),
    trans_id INT(30) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
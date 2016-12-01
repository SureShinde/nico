<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('emjainteractive_shipping_options')} (
  `option_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(255) collate utf8_unicode_ci NOT NULL,
  `label` varchar(255) collate utf8_unicode_ci NOT NULL,
  `type` varchar(25) collate utf8_unicode_ci NOT NULL,
  `apply_to` text collate utf8_unicode_ci NOT NULL,
  `sort_order` tinyint(4) NOT NULL default '0',
  `default_value` varchar(255) collate utf8_unicode_ci NOT NULL,
  `options` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`option_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB;

INSERT INTO {$this->getTable('emjainteractive_shipping_options')} (`option_id`, `code`, `label`, `type`, `apply_to`, `sort_order`, `default_value`, `options`) VALUES (2, 'Account Number', 'Account Number', 'text', 'umosaco', 3, '', ''),
(3, 'Shipping Method', 'Shipping Method', 'select', 'umosaco', 1, '', 'Fedex, UPS, DHL'),
(4, 'Shipping Service', 'Shipping Service', 'select', 'umosaco', 2, '', 'Ground, 2-Day, Overnight'),
(5, 'Account Zip Code', 'Account Zip Code', 'text', 'umosaco', 4, '', '');
");

$installer->endSetup();
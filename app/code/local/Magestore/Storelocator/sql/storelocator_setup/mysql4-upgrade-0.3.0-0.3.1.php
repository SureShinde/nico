<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category     Magestore
 * @package     Magestore_Storelocator
 * @copyright     Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create storelocator table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('storelocator_userinfo')};

CREATE TABLE {$this->getTable('storelocator_userinfo')} (
`id` int(6) unsigned NOT NULL auto_increment,
`tm` varchar(20) NOT NULL default '',
`ref` varchar(250) NOT NULL default '',
`agent` varchar(250) NOT NULL default '',
`ip` varchar(20) NOT NULL default '',
`ip_value` int(11) NOT NULL default '0',
`address` text NOT NULL default '',
`city` varchar(200) NOT NULL default '',
`country` varchar(200) NOT NULL default '',
`state` varchar(200) NOT NULL default '',
`type` varchar(200) NOT NULL default '',
`zipcode` varchar(200) NOT NULL default '',
`domain` varchar(20) NOT NULL default '',
`tracking_page_name` varchar(10) NOT NULL default '',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;        
");
$installer->endSetup();


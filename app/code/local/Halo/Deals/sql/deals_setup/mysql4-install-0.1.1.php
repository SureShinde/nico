<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `deals_gp_sl` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `voucher` varchar(255) NOT NULL,
  `deal_name` varchar(255) NOT NULL,
  `type_of_kit` varchar(255) NOT NULL,
  `sample_pack` varchar(255) NOT NULL,
  `battery_color` varchar(255) NOT NULL,
  `flavor_option_1` varchar(255) NOT NULL,
  `flavor_option_2` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `creadted_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		
SQLTEXT;

$installer->run($sql);
//demo  ALTER TABLE `deals_gp_sl` ADD `sample_pack` VARCHAR(255) NULL AFTER `type_of_kit`;
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.font-size:14px

@package	Plumrocket_Url_Manager-v1.2.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('urlmanager_rules')}` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_path` varchar(512) NOT NULL,
  `target_path` varchar(512) DEFAULT NULL,
  `access` enum('none','guest','open','customer','denied') NOT NULL DEFAULT 'none',
  `pattern` varchar(512) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `{$this->getTable('urlmanager_rules')}` (`request_path`, `target_path`, `access`, `pattern`, `priority`) VALUES
('/login/', '/customer/account/login/', 'none', '\\/login\\/', 10);
");
$installer->endSetup();

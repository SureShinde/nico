<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/gateway')};
CREATE TABLE {$resource->getTableName('helpdesk/gateway')} (
  `gateway_id` int(11) unsigned NOT NULL auto_increment,	
  `name` varchar(255) NOT NULL, 
  `host` varchar(255) NOT NULL, 
  `email` varchar(255) NOT NULL, 
  `login` varchar(255), 
  `password` varchar(255) NOT NULL, 
  `port` varchar(255), 
  `ssl` tinyint(1) unsigned NOT NULL default '1',
  `type` tinyint(1) unsigned NOT NULL default '1',
  `active` tinyint(1) unsigned NOT NULL default '1',
  `default_department` tinyint(1) unsigned NOT NULL default '1',
  `delete_email` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`gateway_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/department')};
CREATE TABLE {$resource->getTableName('helpdesk/department')} (
  `department_id` int(11) unsigned NOT NULL auto_increment,
  `member_id` int(11) unsigned NOT NULL,
  `stores` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `required_login` tinyint(1) unsigned NOT NULL,
  `default_gateway` varchar(255) NOT NULL,
  `auto_notification` tinyint(1) unsigned NOT NULL,
  `description` text default NULL,
  `new_ticket_customer` varchar(255) NOT NULL,
  `reply_ticket_customer` varchar(255) NOT NULL,
  `new_ticket_operator` varchar(255) NOT NULL,
  `reply_ticket_operator` varchar(255) NOT NULL,
  `reassign_ticket_operator` varchar(255) NOT NULL,
  `late_reply_ticket_operator` varchar(255) NOT NULL,
  PRIMARY KEY  (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/member')};
CREATE TABLE {$resource->getTableName('helpdesk/member')} (
  `member_id` int(11) unsigned NOT NULL auto_increment,	
  `name` varchar(255) NOT NULL, 	
  `email` varchar(255) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/template')};
CREATE TABLE {$resource->getTableName('helpdesk/template')} (
  `template_id` int(11) unsigned NOT NULL auto_increment,	
  `title` varchar(255) NOT NULL, 	
  `message` text NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/deme')};
CREATE TABLE {$resource->getTableName('helpdesk/deme')} (
	`department_member` int(11) unsigned NOT NULL auto_increment,	
	`member_id` int(11) unsigned NOT NULL,	
	`department_id` int(11) unsigned NOT NULL,
	PRIMARY KEY  (`department_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/ticket')};
CREATE TABLE {$resource->getTableName('helpdesk/ticket')} (
  `ticket_id` int(11) unsigned NOT NULL auto_increment,
  `member_id` int(11) unsigned NOT NULL,
  `department_id` int(11) unsigned NOT NULL,					
  `subject` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,				
  `created_time` datetime NOT NULL,
  `order` varchar(255) NOT NULL,
  `content` text NOT NULL,														
  `file_attachment` varchar(255) NOT NULL,
  `status` smallint(6) unsigned,							
  `priority` varchar(64),														
  `last_reply_time`	datetime,	
  `reply_by` smallint(6) unsigned,	
  `untreated` smallint(6) unsigned,
  `code_customer` varchar(50) NOT NULL,
  `code_member` varchar(50) NOT NULL,
  PRIMARY KEY  (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('helpdesk/history')};
CREATE TABLE {$resource->getTableName('helpdesk/history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `ticket_id` int(11) unsigned NOT NULL,
  `member_id` int(11) unsigned NOT NULL default '0',
  `department_id` int(11) unsigned NOT NULL default '0',
  `sender` varchar(255) NOT NULL default '',				
  `created_time` datetime default NULL,	
  `content` text NOT NULL,														
  `file_attachment` varchar(255) NOT NULL default '',	
  PRIMARY KEY  (`history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
$installer->run("
	
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_new_ticket_customer` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_reply_ticket_customer` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_new_ticket_operator` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_reply_ticket_operator` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_reassign_ticket_operator` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_late_reply_ticket_operator` smallint(6) unsigned default '0';
ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `status_internal_note_notification` smallint(6) unsigned default '0';

");
$installer->endSetup();
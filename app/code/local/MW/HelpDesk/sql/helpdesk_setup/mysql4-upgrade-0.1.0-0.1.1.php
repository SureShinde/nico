<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$configValuesMap = array(
  	'helpdesk/config/new_ticket_customer' 					=>'helpdesk_config_new_ticket_customer',
	'helpdesk/config/reply_ticket_customer' 				=>'helpdesk_config_reply_ticket_customer',
	'helpdesk/config/new_ticket_operator' 					=>'helpdesk_config_new_ticket_operator',
	'helpdesk/config/reply_ticket_operator' 				=>'helpdesk_config_reply_ticket_operator',
	'helpdesk/config/reassign_ticket_operator' 				=>'helpdesk_config_reassign_ticket_operator',
	'helpdesk/config/late_reply_ticket_operator' 			=>'helpdesk_config_late_reply_ticket_operator',
);
foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}

$installer->startSetup();
$sql ="ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `dcode` varchar(100) NOT NULL;";
$installer->run($sql);
$installer->endSetup();
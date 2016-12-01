<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$configValuesMap = array(
  	'helpdesk/config/internal_note_notification' 	=>'helpdesk_config_internal_note_notification',
);
foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}

$installer->startSetup();
$installer->run("
	ALTER TABLE {$resource->getTableName('helpdesk/department')} ADD `internal_note_notification` varchar(255) NOT NULL;
");
$installer->endSetup();
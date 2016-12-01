<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('customer_flag')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) NULL DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId = $setup->getEntityTypeId('customer');
$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'customer_flag', array(
    'input' => 'text',
    'type' => 'int',
    'label' => 'Customer Flag',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'customer_flag', '999'  //sort_order
);

$installer->endSetup(); ?>
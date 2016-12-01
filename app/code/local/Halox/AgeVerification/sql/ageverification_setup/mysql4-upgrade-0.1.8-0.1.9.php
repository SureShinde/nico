<?php
$installer = $this;

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('customer', 'isVerified');

$installer->endSetup();

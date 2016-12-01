<?php

/**
 * Sunovisio Extensions
 * http://ecommerce.sunovisio.com
 *
 * @extension   Affiliate Network Connector
 * @type        Utility
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Sunovisio
 * @package     Sunovisio_AffiliateNetworkConnector
 * @copyright   Copyright (c) 2012 Sunovisio (http://sunovisio.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId = $setup->getEntityTypeId('customer');
$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'affnetwork', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Affiliate Network',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affnetwork', '1000' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affnetwork');

$oAttribute->save();

$setup->addAttribute('customer', 'affid', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Affiliate ID',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affid', '1010' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affid');

$oAttribute->save();

$setup->addAttribute('customer', 'affoffer', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Affiliate Offer',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affoffer', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affoffer');

$oAttribute->save();

$setup->addAttribute('customer', 'affurl', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Affiliate URL',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affurl', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affurl');

$oAttribute->save();

$setup->addAttribute('customer', 'affdate', array(
    'input' => 'date',
    'type' => 'datetime',
    'label' => 'Affiliate Date',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affdate', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affdate');

$oAttribute->save();

$setup->addAttribute('customer', 'affprimary', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Primary Source',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affprimary', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affprimary');

$oAttribute->save();

$setup->addAttribute('customer', 'affprimarydate', array(
    'input' => 'date',
    'type' => 'datetime',
    'label' => 'Primary Date',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affprimarydate', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affprimaryurl');

$oAttribute->save();

$setup->addAttribute('customer', 'affprimaryurl', array(
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'Primary URL',
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'affprimaryurl', '1020' 
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'affprimaryurl');

$oAttribute->save();

$setup = Mage::getResourceModel('sales/setup', 'default_setup');

$setup->addAttribute('order', 'affnetwork', array(
    'type' => 'varchar',
    'label' => 'Affiliate Network',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'is_configurable' => 0,
));

$setup = Mage::getResourceModel('sales/setup', 'default_setup');

$setup->addAttribute('order', 'affid', array(
    'type' => 'varchar',
    'label' => 'Affiliate ID',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'is_configurable' => 0,
));

$setup = Mage::getResourceModel('sales/setup', 'default_setup');

$setup->addAttribute('order', 'affcommission', array(
    'type' => 'decimal',
    'label' => 'Affiliate Commission',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'is_configurable' => 0,
));

$setup = Mage::getResourceModel('sales/setup', 'default_setup');

$setup->addAttribute('order', 'affcommissiontype', array(
    'type' => 'varchar',
    'label' => 'Affiliate Commission Type',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'is_configurable' => 0,
));

$setup = Mage::getResourceModel('sales/setup', 'default_setup');

$setup->addAttribute('order', 'affreferral', array(
    'type' => 'varchar',
    'label' => 'Affiliate Referral Type',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'is_configurable' => 0,
));

$installer->endSetup();

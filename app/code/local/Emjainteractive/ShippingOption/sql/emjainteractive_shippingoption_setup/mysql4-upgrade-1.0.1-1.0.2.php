<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->run("ALTER TABLE {$this->getTable('sales_flat_quote')} ADD `emjainteractive_shippingoption` TEXT NOT NULL ");
$installer->addAttribute('order', 'emjainteractive_shippingoption', array('is_user_defined' => 1));
$installer->addAttribute('quote', 'emjainteractive_shippingoption', array('is_user_defined' => 1));
<?php
/**
 * Halox
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Halox
 * @package     Halox_CheckoutComment
 * @copyright   Copyright (c) 2016 Halox.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Default helper
 *
 * @author     Halox
 */
class Halox_CheckoutComment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return checkout config value by key and store
     *
     * @param string $key
     * @param Mage_Core_Model_Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $websiteId = Mage::app()->getStore($store)->getWebsiteId();

        if (!isset($this->_config[$websiteId])) {
            $this->_config[$websiteId] = Mage::getStoreConfig('checkout_comment/haloxcheckoutcomment', $store);
        }
        return isset($this->_config[$websiteId][$key]) ? (string)$this->_config[$websiteId][$key] : null;
    }
}
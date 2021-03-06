<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Model_Source_Config_Enableemailsnotifications
{
    const ALL = 'all';
    const CUSTOMERONLY = 'customer';
    const ADMINONLY = 'admin';
    const DISABLED = 'disable';

    public static function toOptionArray()
    {
        return array(
            array('value' => self::ALL, 'label' => Mage::helper('awrma')->__('All')),
            array('value' => self::CUSTOMERONLY, 'label' => Mage::helper('awrma')->__('Customer only')),
            array('value' => self::ADMINONLY, 'label' => Mage::helper('awrma')->__('Admin only')),
            array('value' => self::DISABLED, 'label' => Mage::helper('awrma')->__('Disable'))
        );
    }
}

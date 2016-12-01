<?php
/**
 * Magento Excise Tax Extension
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright (c) 2015 by Yaroslav Voronoy (y.voronoy@gmail.com)
 * @license   http://www.gnu.org/licenses/
 */

class Voronoy_ExtraTax_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EXTRA_TAX_RULE_ACTIVE              = 'extra_tax_settings/extra_tax_rule/active';
    const XML_PATH_EXTRA_TAX_RULE_LABEL               = 'extra_tax_settings/extra_tax_rule/label';

    /**
     * Check If Rule Excise Tax Enabled
     *
     * @return bool
     */
    public function isRuleExtraTaxEnabled()
    {
        $result = (bool) Mage::getStoreConfig(self::XML_PATH_EXTRA_TAX_RULE_ACTIVE);
        return $result;
    }

    /**
     * Get Excise Tax for Shopping Cart Rule
     *
     * @return string
     */
    public function getExtraTaxRuleLabel($ruleId = null, $labelDescription = null)
    {   
	    if(!empty($labelDescription)){
            return $labelDescription;
        }
        $label = '';
        if(!empty($ruleId)){
                $resource = Mage::getSingleton('core/resource');
                $table = $resource->getTableName('salesrule/label');
                $storeId =  Mage::app()->getStore()->getStoreId();
                if(empty($storeId)){                    
                    $orderId = Mage::app()->getRequest()->getParam('order_id');
                    $order = Mage::getModel('sales/order')->load($orderId);
                    $storeId = $order->getStoreId();
                }
                $adapter = $resource->getConnection('core_read');
                $select = $adapter->query("select label from ".$table." where rule_id = '".$ruleId."' AND store_id = '".$storeId."' ");
                $label = $select->fetchAll();            
        }
        if(!empty($label[0]['label'])){
            return $label[0]['label'];
        }
        return (string) Mage::getStoreConfig(self::XML_PATH_EXTRA_TAX_RULE_LABEL);
    }
}
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

class Voronoy_ExtraTax_Model_Quote_Address_Total_Tax_Rule extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Discount calculation object
     *
     * @var Mage_SalesRule_Model_Validator
     */
    protected $_calculator;

    /**
     * Initialize discount collector
     */
    public function __construct()
    {
        $this->_calculator = Mage::getSingleton('voronoy_extratax/salesRule_validator');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('voronoy_extratax')->isRuleExtraTaxEnabled()) {
            return $this;
        }
        parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $this->_calculator->reset($address);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $this->_calculator->initTotals($items, $address);

        $items = $this->_calculator->sortItemsByPriority($items);
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_calculator->process($child);
                    $this->_addAmount($child->getExtraTaxRuleAmount());
                    $this->_addBaseAmount($child->getBaseExtraTaxRuleAmount());
                }
            } else {
                $this->_calculator->process($item);
                $this->_addAmount($item->getExtraTaxRuleAmount());
                $this->_addBaseAmount($item->getBaseExtraTaxRuleAmount());
            }
        }
        $this->addExtraShippingTax($quote);
        $this->_calculator->prepareDescription($address);
    }
     /**
     * @param quote object $quote
     */
    public function addExtraShippingTax($quote) {
        $appliedRuleIds = Mage::getSingleton('checkout/session')->getQuote()->getAppliedRuleIds();
        $rules = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('rule_id', array('in' => $appliedRuleIds));
        foreach ($rules as $rule) {
            $applyToShipping = $rule->getApplyToShipping();
            $amount = $rule->getExtraTaxAmount();
            if (!empty($applyToShipping) && !empty($amount)) {
                $action = $rule->getSimpleAction();
                $quoteShipping = $quote->getShippingAddress()->getShippingAmount();
                if ($action == "by_percent") {
                    $taxAmount = ($quoteShipping * $amount) / 100;
                } else {
                    $taxAmount = $amount;
                }
                $this->_addAmount($taxAmount);
                $this->_addBaseAmount($taxAmount);
            }
        }
    }
    /**
     * Fetch Totals
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return Voronoy_ExtraTax_Model_Quote_Address_Total_Tax_Rule
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('voronoy_extratax')->isRuleExtraTaxEnabled()) {
            return $this;
        }
        $amount = $address->getExtraTaxRuleAmount();
        if ($address->getExtraTaxRuleDescription()) {
            //$discountLabel = Mage::helper('voronoy_extratax')->__('%s (%s)',
            $discountLabel = Mage::helper('voronoy_extratax')->__('%s',
                Mage::helper('voronoy_extratax')->getExtraTaxRuleLabel(null,$address->getExtraTaxRuleDescription()), $address->getExtraTaxRuleDescription());
        } else {
            $discountLabel = Mage::helper('voronoy_extratax')->getExtraTaxRuleLabel();
        }

        if ($amount > 0) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $discountLabel,
                'value' => $amount
            ));
        }
        return $this;
    }
}

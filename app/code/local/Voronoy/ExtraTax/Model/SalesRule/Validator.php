<?php
/**
<<<<<<< HEAD
 * Magento Excise Tax Extension
=======
 * Magento Extra Tax Extension
>>>>>>> 5304ac8fefd1c4c0cdfe3bd924301daf6fa78620
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

class Voronoy_ExtraTax_Model_SalesRule_Validator extends Mage_SalesRule_Model_Validator
{
    /**
<<<<<<< HEAD
     * Quote item Excise Tax calculation process
=======
     * Quote item Extra Tax calculation process
>>>>>>> 5304ac8fefd1c4c0cdfe3bd924301daf6fa78620
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_SalesRule_Model_Validator
     */
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setExtraTaxRuleAmount(0);
        $item->setBaseExtraTaxRuleAmount(0);
        $item->setExtraTaxRulePercent(0);

        $quote         = $item->getQuote();
        $address       = $this->_getAddress($item);
        $itemPrice     = $this->_getItemPrice($item);
        $baseItemPrice = $this->_getItemBasePrice($item);
        if ($itemPrice < 0) {
            return $this;
        }

        $appliedRuleIds = array();
        $this->_stopFurtherRules = false;
        foreach ($this->_getRules() as $rule) {
            if (!$this->_isRuleApplicableForItem($rule, $item)) {
                continue;
            }
            $qty                = $this->_getItemQty($item, $rule);
            $extraTaxAmount     = 0;
            $baseExtraTaxAmount = 0;

            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $extraTaxPercent = min(100, $rule->getExtraTaxAmount());
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $_rulePct = $extraTaxPercent/100;
                    $extraTaxAmount    = ($qty * $itemPrice - $item->getExtraTaxRuleAmount()) * $_rulePct;
                    $baseExtraTaxAmount = ($qty * $baseItemPrice - $item->getBaseExtraTaxRuleAmount()) * $_rulePct;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                        $extraTaxPercent = min(100, $item->getExtraTaxRulePercent()+$extraTaxPercent);
                        $item->setExtraTaxRulePercent($extraTaxPercent);
                    }
                    break;

                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $quoteAmount        = $quote->getStore()->convertPrice($rule->getExtraTaxAmount());
                    $extraTaxAmount     = $qty * $quoteAmount;
                    $baseExtraTaxAmount = $qty * $rule->getExtraTaxAmount();
                    break;
                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    if (empty($this->_rulesItemTotals[$rule->getId()])) {
                        Mage::throwException(Mage::helper('salesrule')->__('Item totals are not set for rule.'));
                    }

                    /**
                     * prevent applying whole cart discount for every shipping order, but only for first order
                     */
                    if ($quote->getIsMultiShipping()) {
                        $usedForAddressId = $this->getCartFixedRuleUsedForAddress($rule->getId());
                        if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                            break;
                        } else {
                            $this->setCartFixedRuleUsedForAddress($rule->getId(), $address->getId());
                        }
                    }
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getExtraTaxAmount();
                    }

                    if ($cartRules[$rule->getId()] > 0) {
                        if ($this->_rulesItemTotals[$rule->getId()]['items_count'] <= 1) {
                            $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                            $baseExtraTaxAmount= $cartRules[$rule->getId()];
                        } else {
                            $discountRate = $baseItemPrice * $qty /
                                $this->_rulesItemTotals[$rule->getId()]['base_items_price'];
                            $maximumItemDiscount = $rule->getExtraTaxAmount() * $discountRate;
                            $quoteAmount = $quote->getStore()->convertPrice($maximumItemDiscount);

                            $baseExtraTaxAmount = $maximumItemDiscount;
                            $this->_rulesItemTotals[$rule->getId()]['items_count']--;
                        }

                        $extraTaxAmount = $quoteAmount;
                        $extraTaxAmount = $quote->getStore()->roundPrice($extraTaxAmount);
                        $baseExtraTaxAmount = $quote->getStore()->roundPrice($baseExtraTaxAmount);

                        $cartRules[$rule->getId()] -= $baseExtraTaxAmount;
                    }
                    $address->setCartFixedRules($cartRules);

                    break;
            }

            $percentKey = $item->getExtraTaxRulePercent();
            /**
             * Process "delta" rounding
             */
            if ($percentKey) {
                $delta      = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                $baseDelta  = isset($this->_baseRoundingDeltas[$percentKey])
                    ? $this->_baseRoundingDeltas[$percentKey]
                    : 0;
                $extraTaxAmount += $delta;
                $baseExtraTaxAmount += $baseDelta;

                $this->_roundingDeltas[$percentKey]     = $extraTaxAmount -
                    $quote->getStore()->roundPrice($extraTaxAmount);
                $this->_baseRoundingDeltas[$percentKey] = $baseExtraTaxAmount -
                    $quote->getStore()->roundPrice($baseExtraTaxAmount);
                $extraTaxAmount = $quote->getStore()->roundPrice($extraTaxAmount);
                $baseExtraTaxAmount = $quote->getStore()->roundPrice($baseExtraTaxAmount);
            } else {
                $extraTaxAmount     = $quote->getStore()->roundPrice($extraTaxAmount);
                $baseExtraTaxAmount = $quote->getStore()->roundPrice($baseExtraTaxAmount);
            }

            /**
             * We can't use row total here because row total not include tax
             * Discount can be applied on price included tax
             */

            $itemExtraTaxRuleAmount = $item->getExtraTaxRuleAmount();
            $itemBaseExtraTaxRuleAmount = $item->getBaseExtraTaxRuleAmount();

            $extraTaxAmount     = $itemExtraTaxRuleAmount + $extraTaxAmount;
            $baseExtraTaxAmount = $itemBaseExtraTaxRuleAmount + $baseExtraTaxAmount;

            $item->setExtraTaxRuleAmount($extraTaxAmount);
            $item->setBaseExtraTaxRuleAmount($baseExtraTaxAmount);

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->_maintainAddressCouponCode($address, $rule);
            $this->_addDiscountDescription($address, $rule);

            if ($rule->getStopRulesProcessing()) {
                $this->_stopFurtherRules = true;
                break;
            }
        }

        $item->setAppliedRuleIds(join(',',$appliedRuleIds));
        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }

    /**
     * Add rule discount description label to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_SalesRule_Model_Rule $rule
     * @return  Mage_SalesRule_Model_Validator
     */
    protected function _addDiscountDescription($address, $rule)
    {
        $description = $address->getExtraTaxRuleDescriptionArray();
        $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStoreId());
        if(empty($ruleLabel)){
          $ruleLabel = $rule->getStoreLabel(0);  
        }
        $label = '';
        if ($ruleLabel) {
            $label = $ruleLabel;
        } else if (strlen($address->getCouponCode())) {
            $label = $address->getCouponCode();
        }

        if (strlen($label)) {
            $description[$rule->getId()] = $label;
        }

        $address->setExtraTaxRuleDescriptionArray($description);

        return $this;
    }

    /**
     * Convert address discount description array to string
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param string $separator
     * @return Mage_SalesRule_Model_Validator
     */
    public function prepareDescription($address, $separator = ', ')
    {
        $descriptionArray = $address->getExtraTaxRuleDescriptionArray();
        /** @see Mage_SalesRule_Model_Validator::_getAddress */
        if (!$descriptionArray && $address->getQuote()->getItemVirtualQty() > 0) {
            $descriptionArray = $address->getQuote()->getBillingAddress()->getExtraTaxRuleDescriptionArray();
        }

        $description = $descriptionArray && is_array($descriptionArray)
            ? implode($separator, array_unique($descriptionArray))
            :  '';

        $address->setExtraTaxRuleDescription($description);
        return $this;
    }

    /**
     * Validate Rule
     *
     * @param $item
     *
     * @return bool
     */
    protected function _isRuleApplicableForItem($rule, $item)
    {
        $address = $this->_getAddress($item);
        /* @var $rule Mage_SalesRule_Model_Rule */
        if (!$this->_canProcessRule($rule, $address)) {
            return false;
        }

        if (!$rule->getActions()->validate($item)) {
            return false;
        }

        return true;
    }

    /**
     * Check if we can process rule
     *
     * @param Mage_SalesRule_Model_Rule $rule
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return bool
     */
    protected function _canProcessRule($rule, $address)
    {
        if ($rule->getExtraTaxAmount() == 0) {
            return false;
        }
        return parent::_canProcessRule($rule, $address);
    }
}

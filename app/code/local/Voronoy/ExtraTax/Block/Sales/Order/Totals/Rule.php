<?php
/**
 * Magento Extra Tax Extension
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

class Voronoy_ExtraTax_Block_Sales_Order_Totals_Rule extends Mage_Core_Block_Abstract
{
    /**
     * Get Source Model
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Add this total to parent
     */
    public function initTotals()
    {
       
        if ((float) $this->getSource()->getExtraTaxRuleAmount() <= 0) {
            return $this;
        }
	    $appliedIds = $this->getSource()->getAppliedRuleIds();
		$extraTaxRuleDescription = $this->getSource()->getExtraTaxRuleDescription();
        if ($this->getSource()->getExtraTaxRuleDescription()) {
         //  $discountLabel = $this->__('%s (%s)', Mage::helper('voronoy_extratax')->getExtraTaxRuleLabel(),$this->getSource()->getExtraTaxRuleDescription());
           $discountLabel = $this->__('%s', Mage::helper('voronoy_extratax')->getExtraTaxRuleLabel($appliedIds,$extraTaxRuleDescription),$this->getSource()->getExtraTaxRuleDescription());
        } else {
           $discountLabel = Mage::helper('voronoy_extratax')->getExtraTaxRuleLabel();
        }
    
        $total = new Varien_Object(array(
            'code'  => 'extra_tax_rule',
            'field' => 'extra_tax_rule_amount',
            'value' => $this->getSource()->getExtraTaxRuleAmount(),
            'label' => $discountLabel
        ));
    //    echo $total;exit;
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
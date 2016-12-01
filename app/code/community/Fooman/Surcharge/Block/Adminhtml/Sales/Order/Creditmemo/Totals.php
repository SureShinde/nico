<?php
/*
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2010 Fooman Limited (http://www.fooman.co.nz)
 */

class Fooman_Surcharge_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals {

    public function initTotals() {
        if ($this->getSource()->getFoomanSurchargeAmount()!= 0){
            $parent = $this->getParentBlock();
            if (Mage::helper('surcharge')->displayBothSales()) {
                $surcharge = new Varien_Object(array(
                            'code'  => 'surcharge',
                            'value' => $this->getSource()->getFoomanSurchargeAmount(),
                            'base_value'=> $this->getSource()->getBaseFoomanSurchargeAmount(),
                            'label' => Mage::helper('surcharge')->__('%s (Excl. Tax)', $this->getSource()->getOrder()->getFoomanSurchargeDescription())
                        ));
                $surchargeIncl = new Varien_Object(array(
                            'code'  => 'surcharge_incl',
                            'value' => $this->getSource()->getFoomanSurchargeAmount() + $this->getSource()->getFoomanSurchargeTaxAmount(),
                            'base_value'=> $this->getSource()->getBaseFoomanSurchargeAmount()+ $this->getSource()->getBaseFoomanSurchargeTaxAmount(),
                            'label' => Mage::helper('surcharge')->__('%s (Incl. Tax)', $this->getSource()->getOrder()->getFoomanSurchargeDescription())
                        ));
                //Work out where to slot in surcharge totals
                if ($parent->getTotal('shipping_incl')) {
                    $parent->addTotal($surcharge,'shipping_incl');
                } elseif ($parent->getTotal('shipping')) {
                    $parent->addTotal($surcharge,'shipping');
                } elseif ($parent->getTotal('subtotal_incl')) {
                    $parent->addTotal($surcharge,'subtotal_incl');
                } else {
                    $parent->addTotal($surcharge,'subtotal');
                }
                //add the inclusive surcharge after the excl surcharge
                $parent->addTotal($surchargeIncl,'surcharge');
            } elseif (Mage::helper('surcharge')->displayIncludeTaxSales()) {
                $surchargeIncl = new Varien_Object(array(
                            'code'  => 'surcharge_incl',
                            'value' => $this->getSource()->getFoomanSurchargeAmount() + $this->getSource()->getFoomanSurchargeTaxAmount(),
                            'base_value'=> $this->getSource()->getBaseFoomanSurchargeAmount()+ $this->getSource()->getBaseFoomanSurchargeTaxAmount(),
                            'label' => $this->getSource()->getOrder()->getFoomanSurchargeDescription()
                        ));
                //Work out where to slot in surcharge totals
                if ($parent->getTotal('shipping_incl')) {
                    $parent->addTotal($surchargeIncl,'shipping_incl');
                } elseif ($parent->getTotal('shipping')) {
                    $parent->addTotal($surchargeIncl,'shipping');
                } elseif ($parent->getTotal('subtotal_incl')) {
                    $parent->addTotal($surchargeIncl,'subtotal_incl');
                } else {
                    $parent->addTotal($surchargeIncl,'subtotal');
                }
            } else {
                $surcharge = new Varien_Object(array(
                            'code'  => 'surcharge',
                            'value' => $this->getSource()->getFoomanSurchargeAmount(),
                            'base_value'=> $this->getSource()->getBaseFoomanSurchargeAmount(),
                            'label' => $this->getSource()->getOrder()->getFoomanSurchargeDescription()
                        ));
                //Work out where to slot in surcharge totals
                if ($parent->getTotal('shipping_incl')) {
                    $parent->addTotal($surcharge,'shipping_incl');
                } elseif ($parent->getTotal('shipping')) {
                    $parent->addTotal($surcharge,'shipping');
                } elseif ($parent->getTotal('subtotal_incl')) {
                    $parent->addTotal($surcharge,'subtotal_incl');
                } else {
                    $parent->addTotal($surcharge,'subtotal');
                }
            }
        }
        return $this;
    }
}
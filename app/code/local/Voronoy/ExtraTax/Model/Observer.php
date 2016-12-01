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
class Voronoy_ExtraTax_Model_Observer {

    /**
     * Process Sales Rule Model Before Save
     *
     * @param $observer
     * @return $this
     */
    public function beforeSaveSalesRuleModel($observer) {
        if (!Mage::helper('voronoy_extratax')->isRuleExtraTaxEnabled()) {
            return $this;
        }

        if (Mage::app()->getRequest()->isPost()) {
            $postData = Mage::app()->getRequest()->getPost();

            if (isset($postData['extra_tax_amount'])) {
                $salesRuleModel = $observer->getEvent()->getDataObject();
                $salesRuleModel->setExtraTaxAmount($postData['extra_tax_amount']);
            }
        }  // print_r($postData);exit;
    }

    /**
     * Prepare Form for Sales Rule
     *
     * @param $observer
     * @return $this
     */
    public function prepareFormSalesRuleEdit($observer) {
        if (!Mage::helper('voronoy_extratax')->isRuleExtraTaxEnabled()) {
            return $this;
        }

        $model = Mage::registry('current_promo_quote_rule');
        if (!$model) {
            return $this;
        }
        /** @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField('extra_tax_amount', 'text', array(
            'name' => 'extra_tax_amount',
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Excise Tax Amount'),
                ), 'discount_amount');
        //  echo $model->getExtraTaxAmount();exit;
        $model->setExtraTaxAmount($model->getExtraTaxAmount() * 1);

        Mage::app()->getLayout()->getBlock('promo_quote_edit_tab_actions')
                ->setChild('form_after', Mage::app()->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap('rule_extra_tax_amount', 'extra_tax_amount')
                        ->addFieldMap('rule_simple_action', 'simple_action')
                        ->addFieldDependence('extra_tax_amount', 'simple_action', array(
                            Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
                            Mage_SalesRule_Model_Rule::BY_FIXED_ACTION,
                            Mage_SalesRule_Model_Rule::CART_FIXED_ACTION))
        );
    }

    /**
     * PayPal prepare request
     *
     * @param $observer
     */
    public function paypalPrepareLineItems($observer) {

        /* @var $cart Mage_Paypal_Model_Cart */
        $cart = $observer->getEvent()->getPaypalCart();
        $address = $cart->getSalesEntity()->getIsVirtual() ?
                $cart->getSalesEntity()->getBillingAddress() : $cart->getSalesEntity()->getShippingAddress();
        $taxAmount = $address->getExtraTaxRuleAmount();
        $cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_TAX, $taxAmount);
    }

    protected function _getFullActionName($request, $delimiter = '_') {
        return $request->getRequestedRouteName() . $delimiter .
                $request->getRequestedControllerName() . $delimiter .
                $request->getRequestedActionName();
    }

    public function insertHtml($observer) {

        if (!Mage::helper('voronoy_extratax')->isRuleExtraTaxEnabled()) {
            return $this;
        }

        $block = $observer->getBlock();
        $class = get_class($block);

        $currentAction = $this->_getFullActionName($block->getRequest());
        //Mage::log('currentaction=='.$currentAction,null,'testby.log');

        $validActions = array(
            'sales_order_invoice',
            'sales_order_view',
            'sales_order_creditmemo',
            'sales_order_printCreditmemo',
            'adminhtml_sales_order_email',
            'checkout_onepage_saveOrder',
            'adminhtml_advancedorderstatus_grid_mass'
        );

        // prepare Email totals
        if (((strpos($class, '_Email_') !== false) || in_array($currentAction, $validActions)) && !Mage::registry('excise_tax_email_template_style')) {
            Mage::register('excise_tax_email_template_style', true);
        }

        // insert totals in Email block only
        if (
                Mage::registry('excise_tax_email_template_style') &&
                (
                $block instanceof Mage_Sales_Block_Order_Totals ||
                $block instanceof Mage_Sales_Block_Order_Creditmemo_Totals ||
                $block instanceof Mage_Sales_Block_Order_Invoice_Totals
                )
        ) {

            $html = $observer->getTransport()->getHtml();
            $html = $this->_prepareTotalsHtml($block, $html);
            $observer->getTransport()->setHtml($html);
        }

        return $observer;
    }

    /**
     * @param $html
     *
     * @return mixed
     */

	 private function _prepareTotalsHtml($block, $html) {  
       
	   /*if(!is_object($block->getAction())){
             return $html;
         }*/

		$order = $this->getDataByParams();
        $storeId = Mage::app()->getStore()->getCode();

        if ($storeId != "halo_wholesale_english") {
            return $html;
        }

        if (!$order) {
            return $html;
        }

		if(is_object($block->getAction())){
			$action = $block->getAction()->getFullActionName();

        }else{
            $action ='cron_invoice_generate';
        }

        //$action = $block->getAction()->getFullActionName();
        $amount = $order->getBaseExtraTaxRuleAmount();

        if (!empty($amount) && $amount != "0.00") {
            $label = Mage::getStoreConfig('extra_tax_settings/extra_tax_rule/label');
            $extraTaxRuleDesc = $order->getExtraTaxRuleDescription();
            if (!empty($extraTaxRuleDesc)) {
                $label = $extraTaxRuleDesc;
            }
            $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = $order->getOrderCurrencyCode() ? $order->getOrderCurrencyCode() : $order->getQuoteCurrencyCode();
            if ($currentCurrencyCode && $baseCurrencyCode != $currentCurrencyCode) {
                $amount = Mage::helper('directory')->currencyConvert($amount, $baseCurrencyCode, $currentCurrencyCode);
                $amount = Mage::app()->getLocale()->currency($currentCurrencyCode)->toCurrency($amount);
            } else {
                $amount = Mage::helper('core')->currency($amount, true, true);
            }
            $html_block = '';

            switch ($action) {
                case 'adminhtml_sales_order_invoice_save':
                    $html_block = '
						<tr class="">
							<td colspan="4" align="right" style="padding:3px 9px">' . Mage::helper('salesrule')->__($label) . '</td>
							<td align="right" style="padding:3px 9px"><span class="price">' . $amount . '</span></td>
						</tr>
					';
                    break;
                case 'sales_order_invoice':
                case 'sales_order_view':

                    $html_block = '';

                    break;

                case 'sales_order_creditmemo':

                    $html_block = '
						<tr class="">
							<td colspan="6" class="a-right">' . Mage::helper('salesrule')->__($label) . '</td>
							<td class="a-right last"><span class="price">' . $amount . '</span></td>
						</tr>
					';

                    break;

                default:

                    $html_block = '
						<tr class="">
							<td colspan="4" align="right" style="padding:3px 9px">' . Mage::helper('salesrule')->__($label) . '</td>
							<td align="right" style="padding:3px 9px"><span class="price">' . $amount . '</span></td>
						</tr>
					';
            }


            if ($amount) {

                $html = str_replace(
                        '<tr class="grand_total', $html_block . "\r\n" . ' <tr class="grand_total', $html
                );
            }
        }
        return $html;
    }

    private function getDataByParams() {
        $data = false;
        $params = Mage::app()->getRequest()->getParams();
        /*
         * case: invoice view/edit/create
         */
        if (isset($params['come_from']) && $params['come_from'] == 'invoice') {
            $data = Mage::getModel('sales/order_invoice')->load($params['invoice_id']);
        } /*
         * case: order edit
         */ elseif (isset($params['come_from']) && $params['come_from'] == 'order') {
            $data = Mage::getModel('sales/order')->load($params['order_id']);
        } /*
         * case: order view
         */ elseif (!isset($params['come_from']) && isset($params['order_id'])) {
            $data = Mage::getModel('sales/order')->load($params['order_id']);
        } /*
         * case: creditmemo view print
         */ elseif (!isset($params['come_from']) && isset($params['creditmemo_id'])) {
            $data = Mage::getModel('sales/order_creditmemo')->load($params['creditmemo_id']);
        }/*
         * case: order edit
         */ elseif (isset($params['action']) && ($params['action'] == 'change_order_status') && isset($params['order_ids'])) {
            $data = Mage::getModel('sales/order')->load($params['order_ids']['0']);
        } /*
         * case: invoice view print
         */ elseif (!isset($params['come_from']) && isset($params['invoice_id'])) {
            $data = Mage::getModel('sales/order_invoice')->load($params['invoice_id']);
        }/*
         * case: when cron runs
         */ elseif (empty($params)) {
            $orderId = Mage::getSingleton('core/session')->getOrderIdForCronToShowExciseTax();
            if (isset($orderId) && !empty($orderId)) {
                $data = Mage::getModel('sales/order')->load($orderId);
                //Mage::getSingleton('core/session')->unsOrderIdForCronToShowShipInsurance();
            } else {
                $data = array();
            }
        } /*
         * case: Order Email Template Processing
         */ else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $data = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            if ($quote->getQuoteCurrencyCode()) {
                $data->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
            } else if ($quote->getOrderCurrencyCode()) {
                $data->setOrderCurrencyCode($quote->getOrderCurrencyCode());
            }
        }

        return $data;
    }

}

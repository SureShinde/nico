<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsTransfer Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Model_Observer {

    /**
     * proccess event customer login success for transfer points
     */
    public function customerRegisterSuccess($observer) {
        $customer_reg = $observer->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;
        try {
            $transfers = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                    ->addFieldToFilter('receiver_email', $customer->getEmail())
                    ->addFieldToFilter('status', Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING)
            ;
        } catch (Exception $exc) {
            echo $exc->getData();
        }
        try {
            foreach ($transfers as $transfer) {
                $now = strtotime(now());
                $complete_time = strtotime($transfer->getCreatedTime()) + $transfer->getHoldingDay() * 86400;
                $transfer->setReceiverCustomerId($customer->getId());
                if ($now >= $complete_time) {
                    $transfer->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING);
                    Mage::helper('rewardpointstransfer')->completeTransfer($transfer);
                } else {
                    $transfer->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING);
                }
                $transfer->save();
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        return $this;
    }

    /**
     * process event customer resgister for version <1.6.0.0
     * @param type $observer
     * @return \Magestore_RewardPointsTransfer_Model_Observer
     */
    public function customerRegisterSuccessForLow($observer) {
        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
            return;
        }
        $customer_reg = $observer->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;
        $transfers = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                ->addFieldToFilter('receiver_email', $customer->getEmail())
                ->addFieldToFilter('status', Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING)
        ;
        try {
            foreach ($transfers as $transfer) {
                $now = strtotime(now());
                $complete_time = strtotime($transfer->getCreatedTime()) + $transfer->getHoldingDay() * 86400;
                $transfer->setReceiverCustomerId($customer->getId());
                if ($now >= $complete_time) {
                    $transfer->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING);
                    Mage::helper('rewardpointstransfer')->completeTransfer($transfer);
                } else {
                    $transfer->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING);
                }
                $transfer->save();
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        return $this;
    }

    public function settingsPost($observer) {
        if (Mage::helper('rewardpointstransfer')->isEnable()) {
            $action = $observer->getEvent()->getControllerAction();
            $notification = $action->getRequest()->getParam('transfer_notification');
            if (Mage::getSingleton('customer/session')->isLoggedIn()
            ) {
                $customerId = Mage::getSingleton('customer/session')->getCustomerId();
                $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
                if (!$rewardAccount->getId()) {
                    $rewardAccount->setCustomerId($customerId)
                            ->setData('point_balance', 0)
                            ->setData('holding_balance', 0)
                            ->setData('spent_balance', 0);
                }
                $rewardAccount->setTransferNotification((boolean) $notification);
                try {
                    $rewardAccount->save();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            }
        }
        return $this;
    }

    public function rewardpoints_transaction_view_detail($observer) {
        $fieldset = $observer->getEvent()->getFieldset();
        $model    = $observer->getEvent()->getModeltransaction();
        $tranferId = $model->getExtraContent();
        $arrExtra = explode("=",$tranferId);
        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($arrExtra[1]);
        switch ($model->getAction()) {
            case "sendpoint":                    
                    $fieldset->addField('sender_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Sender Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $model->getCustomerId())),
                                $model->getCustomerEmail()
                            ),
                    ));
                   
                    $fieldset->addField('receiver_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Receiver Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $transfer->getReceiverCustomerId())),
                                $transfer->getReceiverEmail()
                            ),
                    ));
                    $fieldset->removeField('customer_email'); 
                    break;
                case "refundpoint":
                    $fieldset->addField('sender_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Sender Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $model->getCustomerId())),
                                $model->getCustomerEmail()
                            ),
                    ));
                    
                     $fieldset->addField('receiver_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Receiver Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $transfer->getReceiverCustomerId())),
                                $transfer->getReceiverEmail()
                            ),
                    ));
                    $fieldset->removeField('customer_email'); 
                    break;
                case "receivepoint":
                     $fieldset->addField('sender_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Sender Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $transfer->getSenderCustomerId())),
                                $transfer->getSenderEmail()
                            ),
                    ));
                    
                    $fieldset->addField('receiver_email', 'note', array(
                        'label'     => Mage::helper('rewardpoints')->__('Receiver Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                Mage::app()->getStore()->getUrl('adminhtml/customer/edit', array('id' => $model->getCustomerId())),
                                $model->getCustomerEmail()
                            ),
                    ));
                    $fieldset->removeField('customer_email'); 
                    break;
                default:
                    break;
            }
    }
    
    protected function _getFullActionName($request, $delimiter = '_') {

        return $request->getRequestedRouteName() . $delimiter .
                $request->getRequestedControllerName() . $delimiter .
                $request->getRequestedActionName();
    }

    public function showSpendingPoints($observer) {

        $block = $observer->getBlock();

        $currentAction = $this->_getFullActionName($block->getRequest());
        // insert totals in Email block only
        if (
                $block instanceof Mage_Sales_Block_Order_Creditmemo_Totals ||
                $block instanceof Mage_Adminhtml_Block_Sales_Order_Invoice_Totals ||
                $block instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals ||
                $block instanceof Mage_Sales_Block_Order_Invoice_Totals
        ) {
            $html = $observer->getTransport()->getHtml();
            $html = $this->_prepareTotalsHtml($block, $html);
            $observer->getTransport()->setHtml($html);
        }

        return $observer;
    }

    private function _prepareTotalsHtml($block, $html) {
        $order = $this->getDataByParams();

        if (!$order) {
            return $html;
        }


        if (is_object($block->getAction())) {
            $action = $block->getAction()->getFullActionName();
        } else {
            $action = 'cron_invoice_generate';
        }
        $amount = $order->getRewardpointsSpent();
        $html_block = '';
        switch ($action) {


            case 'sales_order_invoice':

                $html_block = '
                    <tr class="">
                        <td colspan="4" class="a-right">' . Mage::helper('rewardpointstransfer')->__('Spend Points') . '</td>
                        <td class="a-right last"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';

                break;

            case 'adminhtml_sales_order_invoice_email':

                $html_block = '
                    <tr class="">
                        <td colspan="4" align="right" style="font-size:13px;padding:3px 9px">' . Mage::helper('rewardpointstransfer')->__('Spend Points') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';

                break;

            case 'adminhtml_sales_order_creditmemo':
                $amount = $this->getSpentPointRefunded($order->getId());
                $html_block = '
                    <tr class="">
                        <td colspan="6" class="a-right">' . Mage::helper('rewardpointstransfer')->__('Refund Points') . '</td>
                        <td colspan="2" class="a-right last"><span class="price">' . $amount . '</span></td>
                    </tr>
                ';

                break;


            case 'adminhtml_sales_order_creditmemo_email':
                $amount = $this->getSpentPointRefunded($order->getId());
                $html_block = '
                    <tr class="">
                        <td colspan="4" align="right" style="font-size:13px;padding:3px 9px">' . Mage::helper('rewardpointstransfer')->__('Refund Points Spent') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';

                break;

            case 'sales_order_creditmemo':
                $amount = $this->getSpentPointRefunded($order->getId());
                $html_block = '
                    <tr class="">
                        <td colspan="6" align="right" style="font-size:13px;padding:10px 0px 10px 589px;">' . Mage::helper('rewardpointstransfer')->__('Refund Points Spent') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';

                break;

            case 'sales_order_printCreditmemo':
                $amount = $this->getSpentPointRefunded($order->getId());
                $html_block = '
                    <tr class="">
                        <td colspan="6" align="right" style="font-size:13px;padding:3px 9px">' . Mage::helper('rewardpointstransfer')->__('Spend Points') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . '</span></td>
                    </tr>
                ';

                break;

            case 'sales_order_printInvoice':
                $html_block = '
                    <tr class="">
                        <td colspan="6" align="right" style="font-size:13px;padding:3px 9px">' . Mage::helper('rewardpointstransfer')->__('Spend Points') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';

                break;


            default:

                $html_block = '
                    <tr class="">
                        <td colspan="4" align="right" style="font-size:13px;padding:3px 9px">' . Mage::helper('rewardpointstransfer')->__('Spend Points') . '</td>
                        <td colspan="2" align="right"  style="font-size:13px; padding:3px 9px;"><span class="price">' . $amount . ' Halo Points</span></td>
                    </tr>
                ';
        }

        if ($amount > 0) {
            $html = str_replace(
                    '<tr class="grand_total', $html_block . "\r\n" . ' <tr class="grand_total', $html
            );
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
        }/*
         * case: order edit
         */ elseif (isset($params['action']) && ($params['action'] == 'change_order_status') && isset($params['order_ids'][0])) {
            $data = Mage::getModel('sales/order')->load($params['order_ids']['0']);
        } /*
         * case: order view
         */ elseif (!isset($params['come_from']) && isset($params['order_id'])) {
            $data = Mage::getModel('sales/order')->load($params['order_id']);
        } /*
         * case: creditmemo view print
         */ elseif (!isset($params['come_from']) && isset($params['creditmemo_id'])) {
            $data = Mage::getModel('sales/order_creditmemo')->load($params['creditmemo_id']);
        } /*
         * case: invoice view print
         */ elseif (!isset($params['come_from']) && isset($params['invoice_id'])) {
            $data = Mage::getModel('sales/order_invoice')->load($params['invoice_id']);
        }

        return $data;
    }

    public function getSpentPointRefunded($orderId) {
        $pointAmount = Mage::getModel('rewardpoints/transaction')->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('action', 'spending_creditmemo')->getFirstItem()->getPointAmount();
        return $pointAmount;
    }

}

<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Halox_AgeVerification_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {
	
	public function setCollection($collection)
    {
        if ($collection instanceof Mage_Sales_Model_Resource_Order_Grid_Collection){        
            $collection->getSelect()->joinLeft(
                array('order' => $collection->getTable('sales/order')),
                'main_table.entity_id=order.entity_id',
                array(
                    'verification_status'   
            )); 
        }else if ($collection instanceof Mage_Core_Model_Mysql4_Collection_Abstract){
            $collection->getSelect()->joinLeft(
                array('order' => $collection->getTable('sales/order')),
                'main_table.entity_id=order.entity_id',
                array(
                    'verification_status'   
            )); 
        }else if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract){           
            $collection->joinTable('sales/order', 'entity_id=entity_id', array("verification_status" => "verification_status"), null, "left");
        }

        $collection->addFilterToMap('increment_id', 'main_table.increment_id')
            ->addFilterToMap('grand_total', 'main_table.grand_total')
            ->addFilterToMap('base_grand_total', 'main_table.base_grand_total')
            ->addFilterToMap('status', 'main_table.status')
            ->addFilterToMap('store_id', 'main_table.store_id')
            ->addFilterToMap('created_at', 'main_table.created_at');   
        
        return parent::setCollection($collection);
    }

    protected function _prepareColumns() {

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('verification_status', array(
            'header' => Mage::helper('sales')->__('Verified Status'),
            'index' => 'verification_status',
            'width' => '70px',
            'renderer' => 'Halox_AgeVerification_Block_Adminhtml_Order_Renderer_Verificationstatus',
			'type' => 'options',
            'options' => Mage::helper('ageverification')->getVerificationStatusOptions(),
            'filter_condition_callback' => array($this, '_filterAgeverificationStatus'),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action', array(
                'header' => Mage::helper('sales')->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url' => array('base' => '*/sales_order/view'),
                        'field' => 'order_id',
                        'data-column' => 'action',
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _filterAgeverificationStatus($collection, $column)
    {
        if ($collection instanceof Mage_Sales_Model_Resource_Order_Grid_Collection){   
            $collection->addFieldToFilter('order.verification_status' , $column->getFilter()->getCondition());
        }else if ($collection instanceof Mage_Core_Model_Mysql4_Collection_Abstract){
            $collection->addFieldToFilter('order.verification_status' , $column->getFilter()->getCondition());
        }else{
            $collection->addFieldToFilter($column->getIndex() , $column->getFilter()->getCondition());
        }
    }
}

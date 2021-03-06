<?php
/**
 * Yireo DeleteAnyOrder for Magento 
 *
 * @package     Yireo_DeleteAnyOrder
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (c) 2012 Yireo (http://www.yireo.com/)
 * @license     Open Software License
 */

/**
 * DeleteAnyOrder Database model
 */
class Yireo_DeleteAnyOrder_Model_Database
{
    /*
     * List of order-IDs
     */
    protected $order_ids = array();    

    /**
     * Analyze the database for mistakes in the EAV-structure
     * 
     * @access public
     * @param null
     * @return array
     */
    public function getAnalysis()
    {
        $analysis = array();

        // Get the list of valid order-IDs
        $order_ids = $this->getOrderIds();

        // Get the order-models;
        $models = Mage::getModel('deleteanyorder/core')->getOrderModels();

        // Get the invalid lists
        $analysis['downloadable'] = $this->getInvalidDownloads($order_ids);
        $analysis['sales_flat_order'] = $this->getInvalidFlatOrder($order_ids);
        $analysis['sales/order_tax'] = $this->getInvalidTax($order_ids);
        $analysis['sales/order_item'] = $this->getInvalidItem($order_ids);
        $analysis['sales/order_grid'] = $this->getInvalidGrid($order_ids);
        foreach($models as $model) {
            $analysis[$model] = $this->getInvalidEav($model, $order_ids);
        }

        ksort($analysis);
        return $analysis;
    }

    /**
     * Delete all invalid items
     * 
     * @access public
     * @param null
     * @return null
     */
    public function cleanup()
    {
        // Get the analysis array
        $analysis = $this->getAnalysis();

        // Loop through the analysis-result and remove the corresponding objects
        if(!empty($analysis)) {
            foreach($analysis as $list) {
                if(!empty($list)) {
                    foreach($list as $item) {
                        if(is_object($item)) {
                            $item->delete();
                        }
                    }
                }
            }
        }

        // Fix some tables manually
        $invalid_grid = $this->fixInvalidGrid();
    }

    /**
     * Get a list of valid order-IDs
     * 
     * @access public
     * @param null
     * @return array
     */
    public function getOrderIds()
    {
        if(empty($this->order_ids)) {

            $orders = Mage::getResourceModel('sales/order_collection');
            if(!empty($orders)) {
                foreach($orders as $order) {
                    $ids[] = $order->getId();
                }
            }

            $this->order_ids = $ids;
        }

        return $this->order_ids;
    }

    /**
     * Get a list of all the invalid downloadable links
     * 
     * @access public
     * @param array $order_ids
     * @return array
     */
    public function getInvalidDownloads($order_ids = array())
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();   
        $downloadable = (isset($modules['Mage_Downloadable'])) ? $modules['Mage_Downloadable'] : null;
        if($downloadable == null || $downloadable['active'] == false) {
            return array();
        }

        $invalid = array();
        $order_item_ids = array();
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');

        // Perform the query
        $table = Mage::getSingleton('core/resource')->getTableName('downloadable/link_purchased');
        if(!empty($order_ids)) {
            $query = 'SELECT `purchased_id` FROM `'.$table.'` WHERE `order_id` NOT IN ('.implode(',', $order_ids).')';
        } else {
            $query = 'SELECT `purchased_id` FROM `'.$table.'` WHERE `order_id` > 0';
        }
        $rows = $db->query($query)->fetchAll();

        // If there are listings, convert the ID to an object 
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(is_object($row) && isset($row->purchased_id)) {
                    $invalid[] = Mage::getModel('downloadable/link_purchased')->load($row->purchased_id);
                }
            }
        }

        // Get a list of current purchased_ids
        $purchased_ids = array();
        $query = 'SELECT `purchased_id` FROM '.$table;
        $rows = $db->query($query)->fetchAll();
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(is_object($row) && isset($row->purchased_id)) {
                    $purchased_ids[] = $row->purchased_id;
                }
            }
        }

        // Collect the purchased items
        $table = Mage::getSingleton('core/resource')->getTableName('downloadable/link_purchased_item');
        if(!empty($purchased_ids)) {
            $query = 'SELECT `item_id` FROM `'.$table.'` WHERE `purchased_id` NOT IN ('.implode(',', $purchased_ids).')';
        } else {
            $query = 'SELECT `item_id` FROM `'.$table.'` WHERE `purchased_id` > 0';
        }
        $rows = $db->query($query)->fetchAll();

        // If there are listings, convert the ID to an object 
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(is_object($row) && isset($row->item_id)) {
                    $invalid[] = Mage::getModel('downloadable/link_purchased_item')->load($row->item_id);
                }
            }
        }

        // Return the list
        return $invalid;
    }

    /*
     * Get a list of all invalid items in the sales_flat_order table
     * 
     * @access public
     * @param array $order_ids
     * @return array
     */
    public function getInvalidFlatOrder($order_ids = array())
    {
        $invalid = array();

        // Perform the query
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
    
        if(!empty($order_ids)) {
            $query = 'SELECT `entity_id` FROM `'.$table.'` WHERE `entity_id` NOT IN ('.implode(',', $order_ids).')';
        } else {
            $query = 'SELECT `entity_id` FROM `'.$table.'` WHERE `entity_id` > 0';
        }

        try {
            $rows = $db->query($query)->fetchAll();
        } catch(Exception $e) {
            return array();
        }

        // If there are listings, convert the ID to an object 
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(is_object($row) && isset($row->entity_id)) {
                    $invalid[] = Mage::getModel('sales/order')->load($row->entity_id);
                }
            }
        }

        // Return the list
        return $invalid;
    }

    /*
     * Get a list of all invalid items in the sales_flat_order_grid table
     * 
     * @access public
     * @param array $order_ids
     * @return array
     */
    public function getInvalidGrid($order_ids = array())
    {
        $invalid = array();

        // Perform the query
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid');
    
        if(!empty($order_ids)) {
            $query = 'SELECT `entity_id` FROM `'.$table.'` WHERE `entity_id` NOT IN ('.implode(',', $order_ids).')';
        } else {
            $query = 'SELECT `entity_id` FROM `'.$table.'` WHERE `entity_id` > 0';
        }

        try {
            $rows = $db->query($query)->fetchAll();
        } catch(Exception $e) {
            return array();
        }

        return $rows;
    }

    /**
     * Fix the invalid grid-table
     * 
     * @access public
     * @return array
     */
    public function fixInvalidGrid()
    {
        $order_ids = $this->getOrderIds();
        if(!empty($order_ids)) {
            $db = Mage::getSingleton('core/resource')->getConnection('core_read');
            $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid');
            $query = 'DELETE FROM `'.$table.'` WHERE `entity_id` NOT IN ('.implode(',', $order_ids).')';
            try { $db->query($query); } catch(Exception $e) {}

            $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice_grid');
            $query = 'DELETE FROM `'.$table.'` WHERE `order_id` NOT IN ('.implode(',', $order_ids).')';
            try { $db->query($query); } catch(Exception $e) {}

            return true;
        }
    }

    /*
     * Get a list of all the invalid taxes
     * 
     * @access public
     * @param array $order_ids
     * @return array
     */
    public function getInvalidTax($order_ids = array())
    {
        $invalid = array();

        // Perform the query
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('sales/order_tax');
        if(!empty($order_ids)) {
            $query = 'SELECT `tax_id` FROM `'.$table.'` WHERE `order_id` NOT IN ('.implode(',', $order_ids).')';
        } else {
            $query = 'SELECT `tax_id` FROM `'.$table.'` WHERE `order_id` > 0';
        }
        $rows = $db->query($query)->fetchAll();

        // If there are listings, convert the ID to an object 
        if(!empty($rows)) {
            foreach($rows as $row) {
                if(is_object($row) && isset($row->tax_id)) {
                    $invalid[] = Mage::getModel('sales/order_tax')->load($row->tax_id);
                }
            }
        }

        // Return the list
        return $invalid;
    }

    /*
     * Get a list of all the invalid EAV-attributes
     * 
     * @access public
     * @param string $classname
     * @param array $order_ids
     * @return array
     */
    public function getInvalidEav($classname, $order_ids = array())
    {
        $invalid = array();

        // Build the collection
        $collection = Mage::getSingleton($classname)->getCollection()
            //->addAttributeToSelect('id')
            //->addAttributeToSelect('order_id')
        ;

        // If an item has an order_id which not in the list, add to the invalid-list
        try {
            foreach($collection as $index => $item) {
                if($item->getOrderId() > 0 && !in_array($item->getOrderId(), $order_ids)) {
                    $invalid[] = $item;
                }
            }
        } catch(Exception $e) {
            Mage::getSingleton('core/session')->addError('Checking EAV failed: '.$e->getMessage()); 
        }

        // Return the list
        return $invalid;
    }

    /*
     * Get a list of all the invalid EAV-attributes
     * 
     * @access public
     * @param array $order_ids
     * @return array
     */
    public function getInvalidItem($order_ids = array())
    {
        $invalid = array();

        // Build the collection
        $collection = Mage::getSingleton('sales/order_item')->getCollection()
            ->addFieldToFilter('order_id', array('nin' => $order_ids));

        // If an item has an order_id which not in the list, add to the invalid-list
        foreach($collection as $index => $item) {
            if($item->getOrderId() > 0 && !in_array($item->getOrderId(), $order_ids)) {
                $invalid[] = $item;
            }
        }
        return $invalid;
    }

    /*
     * Get the current increment-ID
     * 
     * @access public
     * @param string $entity_type
     * @return int
     */
    public function getCurrentIncrementId($entity_type = 'order')
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');

        switch($entity_type) {
            case 'order': 
                $entity_type_id = 11; 
                $eav_name = 'sales_order';
                $flat_name = 'sales_flat_order';
                break;
            case 'invoice':     
                $entity_type_id = 16; 
                $eav_name = 'sales_order_entity';
                $flat_name = 'sales_flat_invoice';
                break;
            case 'creditmemo': 
                $entity_type_id = 23; 
                $eav_name = 'sales_order_entity';
                $flat_name = 'sales_flat_creditmemo';
                break;
            default: 
                return false;
        }

        try {
            $table = Mage::getSingleton('core/resource')->getTableName($eav_name);
            $query = 'SELECT MAX(increment_id) AS max FROM `'.$table.'` WHERE entity_type_id = '.$entity_type_id;
            $row = $db->query($query)->fetch();
        } catch(Exception $e) {
            $table = Mage::getSingleton('core/resource')->getTableName($flat_name);
            $query = 'SELECT MAX(increment_id) AS max FROM `'.$table.'`';
            $row = $db->query($query)->fetch();
        }

        if(!empty($row)) {
            return $row['max'];
        }

        return false;
    }

    /*
     * Get the latest increment-ID
     * 
     * @access public
     * @param string $entity_type
     * @return int
     */
    public function getLastIncrementId($entity_type = 'order')
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('eav_entity_store');
        $query = 'SELECT * FROM `'.$table.'`';
        $rows = $db->query($query)->fetchAll();

        switch($entity_type) {
            case 'order': $entity_type_id = 11; break;
            case 'invoice': $entity_type_id = 16; break;
            case 'creditmemo': $entity_type_id = 23; break;
            default: return false;
        }

        if(!empty($rows)) {
            foreach($rows as $row) {
                if($entity_type_id == $row['entity_type_id']) {
                    return $row['increment_last_id'];
                }
            }
        }

        return false;
    }
}

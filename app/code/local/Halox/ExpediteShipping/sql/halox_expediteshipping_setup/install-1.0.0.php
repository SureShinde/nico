<?php

/**
 * add column 'cost' to 'sales_flat_quote_shipping_rate' table 
 */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'expedite_orig_cost', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment'   => 'Shipping method cost without the handling fees',
        'length'    => '12,4',
        'nullable'  => true
));   


$installer->endSetup();






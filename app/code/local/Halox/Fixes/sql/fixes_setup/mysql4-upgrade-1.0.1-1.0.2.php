<?php
/**
 * Halox_Fixes module
 *
 * @category    fixes
 * @package     Halox_Fixes
 */

    $installer = $this;
    $installer->startSetup();

    $installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),'customer_flag', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 11,
        'default'    => 1,
        'comment'   => 'customer_flag'
        ));   
    $installer->endSetup();

    
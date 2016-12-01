<?php 
/**
 * Mas_Mascnotes extension by Makarovsoft.com
 * 
 * @category   	Mas
 * @package		Mas_Mascnotes
 * @copyright  	Copyright (c) 2014
 * @license		http://makarovsoft.com/license.txt
 * @author		makarovsoft.com
 */
/**
 * Mascnotes module install script
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('mascnotes/note'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Note ID')
	->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,

		), 'Note')

	->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		), 'Posted By')

	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		), 'Status')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Note Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Note Modification Time')
	->setComment('Note Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('mascnotes/note_store'))
	->addColumn('note_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'Note ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('mascnotes/note_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('mascnotes/note_store', 'note_id', 'mascnotes/note', 'entity_id'), 'note_id', $this->getTable('mascnotes/note'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('mascnotes/note_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Notes To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
	->newTable($this->getTable('mascnotes/note_customer'))
	->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Category ID')
	->addColumn('note_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
	), 'Note ID')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
	), 'Product ID')
	->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		'default'   => '0',
	), 'Position')
	->addIndex($this->getIdxName('mascnotes/note_customer', array('customer_id')), array('customer_id'))
	->addForeignKey($this->getFkName('mascnotes/note_customer', 'note_id', 'mascnotes/note', 'entity_id'), 'note_id', $this->getTable('mascnotes/note'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('mascnotes/note_customer', 'customer_id', 'customer/entity', 'entity_id'),	'customer_id', $this->getTable('customer/entity'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Note to Customer Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();
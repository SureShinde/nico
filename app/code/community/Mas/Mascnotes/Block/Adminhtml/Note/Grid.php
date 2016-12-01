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
 * Note admin grid block
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Note_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('noteGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Grid
	 * 
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('mascnotes/note')->getCollection();
		$this->setCollection($collection);
		$collection->getSelect()->joinLeft(
			array('nc'=>$collection->getTable('mascnotes/note_customer')),
			'nc.note_id=main_table.entity_id',
			array('count(nc.rel_id) as applicable')
		);
		$collection->getSelect()->group('main_table.entity_id');
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Grid
	 * 
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('mascnotes')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
		$this->addColumn('note', array(
			'header'=> Mage::helper('mascnotes')->__('Note'),
			'index' => 'note',
			'width' => '400px',
			'type'	 	=> 'text',
		));
		
		$this->addColumn('applicable', array(
			'header'=> Mage::helper('mascnotes')->__('Customers Count'),
			'index' => 'applicable',
			'filter' => false,
			'width' => '100px',
			'type'	 	=> 'text',

		));
		$this->addColumn('user_id', array(
			'header'=> Mage::helper('mascnotes')->__('Posted By'),
			'index' => 'user_id',
			'width' => '200px',
			'type'	 	=> 'options',
			'options' => Mage::helper('mascnotes')->getAdmins()

		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('mascnotes')->__('Visible'),
			'index'		=> 'status',
			'type'		=> 'options',
			'width'     => '50px',
			'options'	=> array(
				'1' => Mage::helper('mascnotes')->__('Yes'),
				'0' => Mage::helper('mascnotes')->__('No'),
			)
		));
//		if (!Mage::app()->isSingleStoreMode()) {
//			$this->addColumn('store_id', array(
//				'header'=> Mage::helper('mascnotes')->__('Store Views'),
//				'index' => 'store_id',
//				'type'  => 'store',
//				'store_all' => true,
//				'store_view'=> true,
//				'sortable'  => false,
//				'filter_condition_callback'=> array($this, '_filterStoreCondition'),
//			));
//		}
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('mascnotes')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('mascnotes')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('mascnotes')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('mascnotes')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('mascnotes')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('mascnotes')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('mascnotes')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Grid
	 * 
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('note');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('mascnotes')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('mascnotes')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('mascnotes')->__('Change Visibility'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'status' => array(
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('mascnotes')->__('Visible to customer'),
						'values' => array(
								'1' => Mage::helper('mascnotes')->__('Yes'),
								'0' => Mage::helper('mascnotes')->__('No'),
						)
				)
			)
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Mas_Mascnotes_Model_Note
	 * @return string
	 * 
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	/**
	 * get the grid url
	 * @access public
	 * @return string
	 * 
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	/**
	 * after collection load
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Grid
	 * 
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 * @param Mas_Mascnotes_Model_Resource_Note_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Grid
	 * 
	 */
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}
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
 * Note - product relation edit block
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * Set grid params
	 * @access protected
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('product_grid');
		$this->setDefaultSort('position');
		$this->setDefaultDir('ASC');
		$this->setUseAjax(true);
		if ($this->getNote()->getId()) {
			$this->setDefaultFilter(array('in_products'=>1));
		}
	}
	/**
	 * prepare the product collection
	 * @access protected 
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Product
	 * 
	 */
	protected function _prepareCollection() {
		
		$collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

		if ($this->getNote()->getId()){
			$constraint = '{{table}}.note_id='.$this->getNote()->getId();
		}
		else{
			$constraint = '{{table}}.note_id=0';
		}
		$collection->joinField('position',
			'mascnotes/note_customer',
			'position',
			'customer_id=entity_id',
			$constraint,
			'left');
			
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	/**
	 * prepare mass action grid
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Product
	 * 
	 */ 
	protected function _prepareMassaction(){
		return $this;
	}
	/**
	 * prepare the grid columns
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Product
	 * 
	 */
	protected function _prepareColumns() {
		$this->addColumn('in_products', array(
			'header_css_class'  => 'a-center',
			'type'  => 'checkbox',
			'name'  => 'in_products',
			'values'=> $this->_getSelectedProducts(),
			'align' => 'center',
			'index' => 'entity_id'
		));
		
	 $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
	}
	/**
	 * Retrieve selected products
	 * @access protected
	 * @return array
	 * 
	 */
	protected function _getSelectedProducts(){
		$products = $this->getNoteCustomers();
		if (!is_array($products)) {
			$products = array_keys($this->getSelectedProducts());
		}
		return $products;
	}
 	/**
	 * Retrieve selected products
	 * @access protected
	 * @return array
	 * 
	 */
	public function getSelectedProducts() {
		$products = array();
		$selected = Mage::registry('current_note')->getSelectedProducts();
		if (!is_array($selected)){
			$selected = array();
		}
		return $selected;
	}
	/**
	 * get row url
	 * @access public
	 * @return string
	 * 
	 */
	public function getRowUrl($item){
		return $this->getUrl('*/customer/edit', array(
			'id'=>$item->getId()
		));
	}
	/**
	 * get grid url
	 * @access public
	 * @return string
	 * 
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/customersGrid', array(
			'id'=>$this->getNote()->getId()
		));
	}
	/**
	 * get the current note
	 * @access public
	 * @return Mas_Mascnotes_Model_Note
	 * 
	 */
	public function getNote(){
		return Mage::registry('current_note');
	}
	/**
	 * Add filter
	 * @access protected
	 * @param object $column
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tab_Product
	 * 
	 */
	protected function _addColumnFilterToCollection($column){
		// Set custom filter for in product flag
		if ($column->getId() == 'in_products') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
			} 
			else {
				if($productIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
				}
			}
		} 
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}
}
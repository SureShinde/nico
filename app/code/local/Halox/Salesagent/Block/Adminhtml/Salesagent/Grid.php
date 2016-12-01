<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2014
 */
/**
 * Sales Agents admin grid block
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */ 
class Halox_Salesagent_Block_Adminhtml_Salesagent_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
          */
    public function __construct(){ 
        parent::__construct(); 
        $this->setId('salesagentGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return Halox_Salesagent_Block_Adminhtml_Adbanner_Grid
          */
    protected function _prepareCollection(){ 
       $collection = Mage::getModel('halox_salesagent/agent')->getCollection();
	   $salesAgentId = Mage::getSingleton('core/session')->getAgentId();	   
	   if(!empty($salesAgentId)){
	   $collection->addFieldToFilter('id', $salesAgentId);
	   }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	 protected function _prepareColumns(){
        $this->addColumn('id', array(
            'header'    => Mage::helper('halox_salesagent')->__('Id'),
            'index'        => 'id',
            'type'        => 'number'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('halox_salesagent')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));
          $this->addColumn('email', array(
            'header'=> Mage::helper('halox_salesagent')->__('Email'),
            'index' => 'email',
            'type'=> 'text',

        ));
	   $this->addColumn('phone', array(
            'header'=> Mage::helper('halox_salesagent')->__('Phone'),
            'index' => 'phone',
            'type'=> 'text',

        ));
		$this->addColumn('description', array(
            'header'=> Mage::helper('halox_salesagent')->__('Description'),
            'index' => 'description',
            'type'=> 'text',

        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('halox_salesagent')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('halox_salesagent')->__('Enabled'),
                '0' => Mage::helper('halox_salesagent')->__('Disabled'),
            )
        ));
      
      
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('halox_salesagent')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('halox_salesagent')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('halox_salesagent')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('halox_salesagent')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('halox_salesagent')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected     
    */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
		$isAllowed = Mage::helper('halox_salesagent')->isActionAllowed('Massaction');
		if($isAllowed){
        $this->getMassactionBlock()->setFormFieldName('salesagent');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('halox_salesagent')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('halox_salesagent')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('halox_salesagent')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('halox_salesagent')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('halox_salesagent')->__('Enabled'),
                                '0' => Mage::helper('halox_salesagent')->__('Disabled'),
                        )
                )
            )
        ));
        return $this;
		}else{
		 return false;
		}
    }
    /**
     * get the row url
     * @access public
     * @return string
          */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
          */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    /**
     * after collection load
     * @access protected
          */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
	}
<?php

class MW_HelpDesk_Block_Adminhtml_Rules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('rulesGrid');
      	$this->setDefaultSort('rule_id');
      	$this->setDefaultDir('DESC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/rules')->getCollection();
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('rule_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'rule_id',
     	));
      
      	$this->addColumn('name', array(
          'header'    => Mage::helper('helpdesk')->__('Rule Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('from_date', array(
          'header'    => Mage::helper('helpdesk')->__('Date Start'),
          'align'     =>'center',
      	  'width'     => '120px',
      	  'type'      => 'date',
          'index'     => 'from_date',
      	));
      	
      	$this->addColumn('to_date', array(
          'header'    => Mage::helper('helpdesk')->__('Date Expire'),
          'align'     =>'center',
      	  'width'     => '120px',
      	  'type'      => 'date',
          'index'     => 'to_date',
      	));
     
      	$this->addColumn('is_active', array(
          'header'    => Mage::helper('helpdesk')->__('Status'),
          'align'     => 'right',
          'width'     => '100px',
          'index'     => 'is_active',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      	));
	  	
      	$this->addColumn('sort_order', array(
          'header'    => Mage::helper('helpdesk')->__('Priority'),
          'align'     =>'right',
      	  'width'     => '90px',
          'index'     => 'sort_order',
      	));
      	
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('helpdesk')->__('Action'),
                'width'     => '90',
            	'align'     => 'center',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('helpdesk')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('helpdesk')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('helpdesk')->__('XML'));
	  
    	return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rule_id');
        $this->getMassactionBlock()->setFormFieldName('rule');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('helpdesk')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('helpdesk')->__('Are you sure?')
        ));

        
        $this->getMassactionBlock()->addItem('active', array(
             'label'=> Mage::helper('helpdesk')->__('Change status'),
             'url'  => $this->getUrl('*/*/massActive', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'active',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('helpdesk')->__('Status'),
                         'values' => array(
				              1 => 'Enabled',
				              2 => 'Disabled',
				          ),
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
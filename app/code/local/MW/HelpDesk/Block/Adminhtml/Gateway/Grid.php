<?php

class MW_HelpDesk_Block_Adminhtml_Gateway_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('gatewayGrid');
      	$this->setDefaultSort('gateway_id');
      	$this->setDefaultDir('DESC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/gateway')->getCollection();
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('gateway_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'gateway_id',
     	));

      	$this->addColumn('name', array(
          'header'    => Mage::helper('helpdesk')->__('Gateway Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('sender_name', array(
          'header'    => Mage::helper('helpdesk')->__('Sender Name'),
          'align'     =>'left',
          'index'     => 'sender_name',
      	));
      	
      	$this->addColumn('host', array(
          'header'    => Mage::helper('helpdesk')->__('Gateway Host'),
          'align'     =>'left',
          'index'     => 'host',
      	));
      	
      	$this->addColumn('port', array(
          'header'    => Mage::helper('helpdesk')->__('Port'),
          'align'     =>'left',
          'index'     => 'port',
      	));
      	
      	$this->addColumn('type', array(
          'header'    => Mage::helper('helpdesk')->__('Type'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'type',
          'type'      => 'options',
          'options'   => array(
			  1 => 'IMAP',
              2 => 'POP3',
          ),
      	));
      	
      	$this->addColumn('active', array(
          'header'    => Mage::helper('helpdesk')->__('Active (Yes/No)'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'active',
          'type'      => 'options',
          'options'   => array(
			  1 => 'Yes',
              2 => 'No',
          ),
      	));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('helpdesk')->__('Action'),
                'width'     => '100',
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
        $this->setMassactionIdField('gateway_id');
        $this->getMassactionBlock()->setFormFieldName('gateway');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('helpdesk')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('helpdesk')->__('Are you sure?')
        ));

        
        $this->getMassactionBlock()->addItem('active', array(
             'label'=> Mage::helper('helpdesk')->__('Change active'),
             'url'  => $this->getUrl('*/*/massActive', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'active',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('helpdesk')->__('Active'),
                         'values' => array(
				              1 => 'Yes',
				              2 => 'No',
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
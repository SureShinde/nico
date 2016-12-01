<?php

class MW_HelpDesk_Block_Adminhtml_Department_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('departmentGrid');
      	$this->setDefaultSort('department_sort_order');
      	$this->setDefaultDir('ASC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/department')->getCollection();
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
  		
      	$this->addColumn('department_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'department_id',
     	));
      
      	$this->addColumn('name', array(
          'header'    => Mage::helper('helpdesk')->__('Department Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('dcode', array(
          'header'    => Mage::helper('helpdesk')->__('Dept. Code'),
          'align'     =>'left',
      	  'width'     => '100px',
          'index'     => 'dcode',
      	));
      	
      	
      	
      	$this->addColumn('required_login', array(
          'header'    => Mage::helper('helpdesk')->__('Required Login'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'required_login',
          'type'      => 'options',
          'options'   => array(
			  1 => 'Yes',
              2 => 'No',
          ),
      	));

//      	$this->addColumn('department_sort_order', array(
//          'header'    => Mage::helper('helpdesk')->__('Dept. Sort Order'),
//          'align'     =>'right',
//      	'width'     => '100px',
//          'index'     => 'department_sort_order',
//      	));
      	
      	$this->addColumn('department_sort_order',
            array(
                'header'=> Mage::helper('helpdesk')->__('Sort Order'),
                'width' => '100px',
            	'align'     =>'right',
                'type'  => 'number',
                'validate_class'    => 'validate-number validate-digits',
                'index' => 'department_sort_order',
            	'edit_only'         => true,          
                'editable'          => true,
            	'sortable'      => false,
               	'renderer'  => 'helpdesk/adminhtml_renderer_setorderdept',
            
        ));
      	
      	$this->addColumn('active', array(
          'header'    => Mage::helper('helpdesk')->__('Active'),
          'align'     => 'center',
          'width'     => '100px',
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
	  	//$this->setTemplate('mw_helpdesk/grid.phtml');
    	return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('department_id');
        $this->getMassactionBlock()->setFormFieldName('department');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('helpdesk')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('helpdesk')->__('Are you sure?')
        ));

        
        $this->getMassactionBlock()->addItem('active', array(
             'label'=> Mage::helper('helpdesk')->__('Active'),
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
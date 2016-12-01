<?php
class MW_HelpDesk_Block_Adminhtml_Member_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('memberGrid');
      	$this->setDefaultSort('member_id');
      		$this->setDefaultDir('DESC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/member')->getCollection();
//    	$collection->getSelect()->join('mw_departments',
//                   		'main_table.member_id=mw_departments.member_id',
//                    	array('department_id' =>'mw_departments.name') 
//                   );  
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('member_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'member_id',
     	));
     	
     	$this->addColumn('name', array(
          'header'    => Mage::helper('helpdesk')->__('Staff Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('email', array(
          'header'    => Mage::helper('helpdesk')->__('Email Address'),
          'align'     =>'left',
          'index'     => 'email',
      	));
       	
//	    $this->addColumn('department_id', array(
//          'header'    => Mage::helper('helpdesk')->__('Moderator'),
//          'align'     =>'left',
//          'index'     => 'department_id',
//		  'type'      => 'text',
//        ));

      	$this->addColumn('active', array(
          'header'    => Mage::helper('helpdesk')->__('Active'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'active',
          'type'      => 'options',
      	  'options'   => array(
              1 => Mage::helper('helpdesk')->__('Yes'),
              2 => Mage::helper('helpdesk')->__('No'),
          )
      	));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('helpdesk')->__('Action'),
                'width'     => '50',
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
        $this->setMassactionIdField('member_id');
        $this->getMassactionBlock()->setFormFieldName('member');

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
				              1 => Mage::helper('helpdesk')->__('Yes'),
				              2 => Mage::helper('helpdesk')->__('No'),
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
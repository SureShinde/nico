<?php

class MW_HelpDesk_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('templateGrid');
      	$this->setDefaultSort('template_id');
      	$this->setDefaultDir('DESC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/template')->getCollection();
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('template_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '80px',
          'index'     => 'template_id',
     	));

      	$this->addColumn('title', array(
          'header'    => Mage::helper('helpdesk')->__('Quick Response Title'),
          'align'     =>'left',
          'index'     => 'title',
      	));
      	
      	$categories = unserialize(Mage::getStoreConfig('helpdesk/config/category_response'));
      	$dur = array();
      	foreach ($categories as $cate){
      		$dur[$cate['id_category']] = $cate['name_category'];
      	}
        
        $this->addColumn('id_category', array(
			'header'    => Mage::helper('helpdesk')->__('Response Folder'),
			'align'     =>'left',
			'index'     => 'id_category',
	        'type' => 'options',
	        'options' => $dur,
        	'width'  => '150', 		
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
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template');

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
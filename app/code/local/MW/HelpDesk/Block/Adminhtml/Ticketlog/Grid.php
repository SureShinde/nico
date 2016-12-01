<?php

class MW_HelpDesk_Block_Adminhtml_Ticketlog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('ticketlogGrid');
      	$this->setDefaultSort('date_update');
      	$this->setDefaultDir('DESC');
     	$this->setSaveParametersInSession(true);
  	}

  	protected function _prepareCollection()
  	{
    	$collection = Mage::getModel('helpdesk/ticketlog')->getCollection();
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
      	$this->addColumn('id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
     	));
      
      	$this->addColumn('date_update', array(
          'header'    => Mage::helper('helpdesk')->__('Date'),
          'align'     =>'center',
      	  'width'     => '180px',
      	  'type'      => 'datetime',
      	  'gmtoffset' => true,
          'index'     => 'date_update',
      	));
      	
     	$this->addColumn('code_id', array(
          'header'    => Mage::helper('helpdesk')->__('Ticket ID'),
          'align'     =>'center',
          'index'     => 'code_id',
     	  'width'     => '120px',
      	));
      	
      	$this->addColumn('customer_email', array(
          'header'    => Mage::helper('helpdesk')->__('Customer Email'),
          'align'     =>'left',
          'index'     => 'customer_email',
      	));
      	
//      	$this->addColumn('activity', array(
//          'header'    => Mage::helper('helpdesk')->__('Activity'),
//          'align'     =>'left',
//          'index'     => 'activity',
//      	  'frame_callback' => array($this, 'decorateActivity'),
//      	));

      	$this->addColumn('activity', array(
          'header'    => Mage::helper('helpdesk')->__('Activity'),
          'align'     => 'left',
          'width'     => '170px',
          'index'     => 'activity',
          'type'      => 'options',
          'options'   => array(
			  'Creating New Ticket' => Mage::helper('helpdesk')->__('Creating New Ticket'),
      		  'Customer Viewing' => Mage::helper('helpdesk')->__('Customer Viewing'),
              'Staff Response' => Mage::helper('helpdesk')->__('Staff Response'),
      		  'Customer Response' => Mage::helper('helpdesk')->__('Customer Response'),
              'Staff Updating' => Mage::helper('helpdesk')->__('Staff Updating'),
      		  'Staff Viewing' => Mage::helper('helpdesk')->__('Staff Viewing'),
          ),
          //'frame_callback' => array($this, 'decorateActivity'),
      	));
      	
      	$this->addColumn('staff_email', array(
          'header'    => Mage::helper('helpdesk')->__('Staff Email'),
          'align'     =>'left',
          'index'     => 'staff_email',
      	));
      	
      	$this->addColumn('status', array(
            'header' => Mage::helper('helpdesk')->__('Status'),
            'align' => 'center',
            'width' => '230px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('helpdesk/config_source_status')->getOptionArrayGrid(),
            'frame_callback' => array($this, 'decorateStatus'),
        	'filter_condition_callback' => array($this, '_filterStatutsCondition'),
        ));
     
		$this->addExportType('*/*/exportCsv', Mage::helper('helpdesk')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('helpdesk')->__('XML'));
	  
    	return parent::_prepareColumns();
	}

	public function decorateStatus($value, $row, $column, $isExport) {
        $class = '';$style = '';
        switch ($row->getStatus()) {
			case MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW :
                $style = 'color: #003DF5; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN :
                $style = 'color: #2F2F2F; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING :
                $style = 'color: #E26703; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED :
                $style = 'color: #B9CCDD; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
			case MW_HelpDesk_Model_Config_Source_Status::STATUS_ONHOLD :
                $style = 'color: #2F2F2F; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
        }
//        if ($row->getUntreated() == 2) {
//        	$style = 'color: red; text-transform: uppercase; font: bold 14px/18px Arial,Helvetica,sans-serif;';
//        }
        return '<span style ="' . $style . '"><span>' . $value . '</span></span>';
    }
    
	public function decorateActivity($value, $row, $column, $isExport) {
        $class = '';$style = '';
        switch ($row->getActivity()) {
			case $row->getActivity():
                $style = 'color: #2F2F2F; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
        }
//        if ($row->getUntreated() == 2) {
//        	$style = 'color: red; text-transform: uppercase; font: bold 14px/18px Arial,Helvetica,sans-serif;';
//        }
        return '<span style ="' . $style . '"><span>' . $value . '</span></span>';
    }
    
	protected function _filterStatutsCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
		if($value == MW_HelpDesk_Model_Config_Source_Status::STATUS_ACTIVE){
			 $this->getCollection()->addFieldToFilter('status', array(1, 4));
		}else{
			$this->getCollection()->addFieldToFilter('status', array($value));
		}
       
    }
	
	
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ticketlog');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('helpdesk')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('helpdesk')->__('Are you sure?')
        ));

        
//        $this->getMassactionBlock()->addItem('active', array(
//             'label'=> Mage::helper('helpdesk')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massActive', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'active',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('helpdesk')->__('Status'),
//                         'values' => array(
//				              1 => 'Enabled',
//				              2 => 'Disabled',
//				          ),
//                     )
//             )
//        ));
        return $this;
    }

//  public function getRowUrl($row)
//  {
//      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
//  }

}
<?php
class MW_HelpDesk_Block_Adminhtml_Ticket_Notice extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('noticeGrid');
      	$this->setDefaultSort('ticket_id');
      	$this->setUseAjax(true);
      	$this->setDefaultDir('DESC');
      	$this->setSaveParametersInSession(true);
      	$this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }
    
    protected function _prepareCollection()
    {
    	$collection = Mage::getResourceModel('helpdesk/ticket_collection')
    				->addFilter('untreated',2);
     	$this->setCollection($collection);
     	return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('ticket_id', array(
          'header'    => Mage::helper('helpdesk')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'ticket_id',
     	));
      
      	$this->addColumn('created_time', array(
          'header'    =>  Mage::helper('helpdesk')->__('Time Created'),
          'type'      =>  'datetime',
          'align'     =>  'center',
          'index'     =>  'created_time',
      	  'width'     => '121px',
          'gmtoffset' => true,
          'default'   =>  ' ---- '
       	));

      	$this->addColumn('subject', array(
          'header'    => Mage::helper('helpdesk')->__('Subject'),
          'align'     =>'left',
          'index'     => 'subject',
      	));
      	
      	$this->addColumn('sender', array(
          'header'    => Mage::helper('helpdesk')->__('Sender'),
          'align'     =>'left',
      	  'width'     => '120px',
          'index'     => 'sender',
      	));
      	
      	$this->addColumn('replies', array(
          'header'    => Mage::helper('helpdesk')->__('Replies'),
          'align'     =>'left',
      	  'type'      => 'number',
          'index'     => 'replies',
      	));
      	
      	$this->addColumn('priority', array(
          'header'    => Mage::helper('helpdesk')->__('Priority'),
          'align'     =>  'center',
          'width'     => '80px',
          'index'     => 'priority',
          'type'      => 'options',
          'options'   => Mage::getModel('helpdesk/config_source_priority')->getOptionArray(),
      	  'frame_callback' => array($this, 'decoratePriority'),
      	));
      	
      	$this->addColumn('status', array(
          'header'    => Mage::helper('helpdesk')->__('Status'),
          'align'     =>  'center',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
      	  'options' => Mage::getModel('helpdesk/config_source_status')->getOptionArray(),
          'frame_callback' => array($this, 'decorateStatus'),
      	));
      	
      	$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('helpdesk')->__('Action'),
                'width'     => '40',
                'type'      => 'action',
            	'align'     =>  'center',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('helpdesk')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                //'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    public function decoratePriority($value, $row, $column, $isExport)
    {
        $classPriority = '';
        switch ($row->getPriority()) {
            case MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_NORMAL :
                $classPriority = 'grid-severity-notice';
                break;
            case MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_HIGHT : 
                $classPriority = 'grid-severity-major';
                break;
            case MW_HelpDesk_Model_Config_Source_Priority::PRIORITY_EMERGENCY :
            	$classPriority = 'grid-severity-critical'; 
                break;
        }
        return '<span class="'.$classPriority.'"><span>'.$value.'</span></span>';
    }
    
    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN :
            	$style = 'color: #2F2F2F; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING :
                 $style = 'color: #E26703; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
                break;
            case MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED :
            	 $style = 'color: #B9CCDD; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;'; 
                break;
        }
        if ($row->getUntreated() == 2) {
        	$style = 'color: red; text-transform: uppercase; font: bold 14px/18px Arial,Helvetica,sans-serif;';
        }
        return '<span style ="'.$style.'"><span>'.$value.'</span></span>';
    }
    
  	public function getRowUrl($row)
  	{
      	return $this->getUrl('*/*/view', array('id' => $row->getId()));
  	}

  	public function getGridUrl()
  	{
        return $this->getUrl('*/*/grid', array('_current'=>true));
  	}
}

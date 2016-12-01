<?php
class MW_Helpdesk_Block_Adminhtml_Customer_Edit_Tab_Myticket
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{   
    public function __construct()
    {
    	parent::__construct();
    	$this->setId('customer_myticket');
        $this->setDefaultSort('ticket_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
   
    public function getTabLabel()
    {
        return Mage::helper('customer')->__('Help Desk Tickets');
    }

    public function getTabTitle()
    {
        return Mage::helper('customer')->__('Help Desk Tickets ');
    }

    public function canShowTab()
    {
        return true;
    }

//    public function setJsObjectName(){
//    	return "fraudJsObject";
//    }

    public function isHidden()
    {
        return false;
    }

	public function getAfter()
    {
        return 'tags';
    }
    
   protected function _prepareCollection() {
        $id = $this->getRequest()->getParam('id');
        $cus_collection = Mage::getModel('customer/customer')->load($id);
        $cus_email = $cus_collection->getEmail();
        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
                      ->addFieldToFilter('sender', $cus_email);
        //Mage::log('ccccccccccccc '.$collection->getSelect());                       
        $this->setCollection($collection);
        $filter = $this->getParam('filter');
        $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
        return parent::_prepareCollection();
        //return $this;
    }
// 	protected function _addColumnFilterToCollection($column)
//    {
//    	
//    	$order_id = $this->getRequest()->getParam('order_id');
//        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
//        			  ->addFieldToFilter('order', $order_id);
//        parent::_addColumnFilterToCollection($column);
//       
//        return $this;
//    }
    
    protected function _prepareColumns() {	
        $this->addColumn('ticket_id', array(
            'header' => Mage::helper('sales')->__('ID'),
            'align' => 'right',
            'width' => '40px',
            'index' => 'ticket_id',
        ));

        $this->addColumn('last_reply_time', array(
            'header' => Mage::helper('helpdesk')->__('Time Updated'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'last_reply_time',
            'width' => '150px',
            'gmtoffset' => true,
            'default' => ' ---- '
        ));

        $this->addColumn('code_id', array(
            'header' => Mage::helper('helpdesk')->__('Ticket Id'),
            'align' => 'left',
            'index' => 'code_id',
        ));

        $this->addColumn('subject', array(
            'header' => Mage::helper('helpdesk')->__('Subject'),
            'align' => 'left',
            'index' => 'subject',
        ));

        $this->addColumn('sender', array(
            'header' => Mage::helper('helpdesk')->__('Sender'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'sender',
        ));

        $departments = array();
        $collection = Mage::getModel('helpdesk/department')->getCollection();
        foreach ($collection as $department) {
            $departments[$department->getId()] = $department->getName();
        }

        $this->addColumn('department_id', array(
            'header' => Mage::helper('helpdesk')->__('Department'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'department_id',
            'type' => 'options',
            'options' => $departments
        ));

        $operators = array();
        $collection = Mage::getModel('helpdesk/member')->getCollection();
        foreach ($collection as $operator) {
            $operators[$operator->getId()] = $operator->getName();
        }

        $this->addColumn('member_id', array(
            'header' => Mage::helper('helpdesk')->__('Staff'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'member_id',
            'type' => 'options',
            'options' => $operators
        ));

        $this->addColumn('quicknote', array(
            'header' => Mage::helper('helpdesk')->__('Quick Note'),
            'index' => 'quicknote',
            //'renderer' => 'helpdesk/adminhtml_renderer_tags',
        ));
        
        $this->addColumn('tag_id', array(
            'header' => Mage::helper('helpdesk')->__('Tags'),
            'index' => 'tag_id',
            //'filter'    => false,
            //'sortable'  => false,
            'renderer' => 'helpdesk/adminhtml_renderer_tags',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('helpdesk')->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('helpdesk/config_source_status')->getOptionArrayGrid(),
            'frame_callback' => array($this, 'decorateStatus'),
        	'filter_condition_callback' => array($this, '_filterStatutsCondition'),
        ));

        $this->addColumn('priority', array(
            'header' => Mage::helper('helpdesk')->__('Priority'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'priority',
            'type' => 'options',
            'options' => Mage::getModel('helpdesk/config_source_priority')->getOptionArray(),
            'frame_callback' => array($this, 'decoratePriority'),
        ));

		$this->addColumn('abc', array(
            'header' => Mage::helper('helpdesk')->__('Action'),
            'width' => '60',
            'type' => 'action',
            'align' => 'center',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('helpdesk')->__('View'),
                    //'url' => array('base' => 'hdadmin/adminhtml_ticket/view'),
                    'url' => array('base' => 'adminhtml/hdadmin_ticket/view'),
                	'target'=>"_tab",
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));
		
        return parent::_prepareColumns();
    }

    public function decoratePriority($value, $row, $column, $isExport) {
        if ($row->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED) {
            $style = 'color: #B9CCDD; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
            return '<span style ="' . $style . '"><span>' . $value . '</span></span>';
        }

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
        return '<span class="' . $classPriority . '"><span>' . $value . '</span></span>';
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport) {
        $class = ''; $style = '';
        switch ($row->getStatus()) {
        	case MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW :
                $style = 'color: #2F2F2F; text-transform: uppercase; font: bold 12px/16px Arial,Helvetica,sans-serif;';
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

    public function getGridUrl() {
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('adminhtml/hdadmin_ticket/custicketGrid', array('_current' => true));
    }
	
	public function getRowUrl($row) {
    	return $this->getUrl('adminhtml/hdadmin_ticket/view', array('id' => $row->getId()));
    }
    
} 
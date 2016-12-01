<?php
class MW_Helpdesk_Block_Adminhtml_Sales_Order_View_Fraud
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{   
    public function __construct()
    {
    	parent::__construct();
    	$this->setId('fraud');
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
        return Mage::helper('sales')->__('Help Desk Tickets');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Help Desk Tickets ');
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

 	public function getOrder(){
        return Mage::registry('current_order');
    }
    
    protected function _prepareCollection() {
		$order_id = $this->getRequest()->getParam('order_id');
        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
        			  ->addFieldToFilter('order', $order_id);
        //Mage::log('ccccccccccccc '.$collection->getSelect());	     			  
        $this->setCollection($collection);
        $filter = $this->getParam('filter');
    	$filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
        return parent::_prepareCollection();
        //return $this;
    }
 	protected function _addColumnFilterToCollection($column)
    {
    	
    	$order_id = $this->getRequest()->getParam('order_id');
        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
        			  ->addFieldToFilter('order', $order_id);
        parent::_addColumnFilterToCollection($column);
       
        return $this;
    }
    
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
            'options' => Mage::getModel('helpdesk/config_source_status')->getOptionArray(),
            'frame_callback' => array($this, 'decorateStatus'),
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
//        if ($row->getUntreated() == 2) {
//        	$style = 'color: red; text-transform: uppercase; font: bold 14px/18px Arial,Helvetica,sans-serif;';
//        }
        return '<span style ="' . $style . '"><span>' . $value . '</span></span>';
    }

//    public function getRowClass(Varien_Object $row) {
//        if ($row->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN) {
//            $flag = Mage::getModel('helpdesk/ticket')
//                    ->missedTicket($row->getLastReplyTime(), $row->getStepReplyTime());
//            if ($flag) {
//                return 'unread';
//            }
//        }
//    }

//    protected function _prepareMassaction() {
//    	$currentUrl = $this->helper('core/url')->getCurrentUrl();
//    	Mage::log('lllllllllllllllllllll '.$currentUrl);
//        $this->setMassactionIdField('ticket_id');
//        $this->getMassactionBlock()->setFormFieldName('ticket');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//            'label' => Mage::helper('helpdesk')->__('Delete'),
//            'url' => $this->getUrl('*/*/massDelete'),
//            'confirm' => Mage::helper('helpdesk')->__('Are you sure?')
//        ));
//
//        $statuses = Mage::getSingleton('helpdesk/config_source_status')->getOptionArray();
//        array_unshift($statuses, array('label' => '', 'value' => ''));
//        $this->getMassactionBlock()->addItem('status', array(
//            'label' => Mage::helper('helpdesk')->__('Change Status'),
//            'url' => $this->getUrl('adminhtml/hdadmin_ticket/massStatus', array('_current' => true)),
//            'additional' => array(
//                'visibility' => array(
//                    'name' => 'status',
//                    'type' => 'select',
//                    'class' => 'required-entry',
//                    'label' => Mage::helper('helpdesk')->__('Status'),
//                    'values' => $statuses,
//                )
//            )
//        ));
//
//        $priorities = Mage::getSingleton('helpdesk/config_source_priority')->getOptionArray();
//        array_unshift($priorities, array('label' => '', 'value' => ''));
//        $this->getMassactionBlock()->addItem('priority', array(
//            'label' => Mage::helper('helpdesk')->__('Change Priority'),
//            'url' => $this->getUrl('adminhtml/hdadmin_ticket/massPriority', array('_current' => true)),
//            'additional' => array(
//                'visibility' => array(
//                    'name' => 'priority',
//                    'type' => 'select',
//                    'class' => 'required-entry',
//                    'label' => Mage::helper('helpdesk')->__('Priority'),
//                    'values' => $priorities,
//                )
//            )
//        ));
//        return $this;
//    }

//    public function getRowUrl($row) {
//    	
//        return $this->getUrl('adminhtml/hdadmin_ticket/view', array('id' => $row->getId(), 'action' => Mage::getSingleton('core/session')->getMenuSession()));
//    }

    public function getGridUrl() {
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('adminhtml/hdadmin_ticket/ticketGrid', array('_current' => true));
    }
    
} 
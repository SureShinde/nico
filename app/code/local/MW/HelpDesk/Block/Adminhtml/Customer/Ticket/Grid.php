<?php

class MW_HelpDesk_Block_Adminhtml_Customer_Ticket_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        if (Mage::app()->getRequest()->getActionName() == 'open') {
            Mage::getSingleton('core/session')->setMenuSession('open');
        } elseif (Mage::app()->getRequest()->getActionName() == 'index') {
            Mage::getSingleton('core/session')->setMenuSession('index');
        };
        parent::__construct();
        $this->setId('ticketGrid');
        $this->setDefaultSort('last_reply_time');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
        //$this->_filterVisibility = false;
        //$this->_pagerVisibility  = false;
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
//        if ($this->getRequest()->getParam('tags')) {
//            $tag = str_replace('+', ' ', $this->getRequest()->getParam('tags'));
//            // load tags
//            $tags = '';
//            $collection = Mage::getModel('helpdesk/tag')->getCollection()
//                    ->addFieldToFilter('name', array('eq' => $tag));
//            foreach ($collection as $tag) {
//                $tags[] = $tag->getTicketId();
//            }
//            $collection = Mage::getResourceModel('helpdesk/ticket_collection')
//                    ->addFieldToFilter('ticket_id', array('in' => $tags));
//            $this->setCollection($collection);
//        } else {
//            $collection = Mage::getResourceModel('helpdesk/ticket_collection');
//            $this->setCollection($collection);
//            if (Mage::app()->getRequest()->getActionName() == 'open') {
//                $this->_setFilterValues(array('status' => 1));
//                Mage::getSingleton('core/session')->setMenuSession('open');
//            } elseif (Mage::app()->getRequest()->getActionName() == 'index') {
//                Mage::getSingleton('core/session')->setMenuSession('index');
//            };
//        }
        $ticketId = Mage::getModel('core/session')->getTicketId();
        $model = Mage::getModel('helpdesk/ticket')->load($ticketId);
        $relatedTicket = Mage::getResourceModel('helpdesk/ticket_collection')->getRelatedTicketByCustom($model->getSender(), $model->getId());
        
        if($relatedTicket->getSize() > 0){
            $this->setCollection($relatedTicket);
        }
        return parent::_prepareCollection();
    }

//    protected function _addColumnFilterToCollection($column) {
//        if ($this->getCollection()) {
//            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
//            if ($column->getFilterConditionCallback()) {
//                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
//            } else {
//                $cond = $column->getFilter()->getCondition();
//                if ($field && isset($cond)) {
//                    if ($field == 'tag_id') {
//                        // load tags
//                        $tags = array();
//                        $collection = Mage::getModel('helpdesk/tag')->getCollection()
//                                ->addFieldToFilter('name', array('eq' => trim($cond['like'], '%')));
//                        foreach ($collection as $tag) {
//                            $tags[] = $tag->getTicketId();
//                        }
//                        $this->getCollection()->addFieldToFilter('ticket_id', array('in' => $tags));
//                    } else {
//                        $this->getCollection()->addFieldToFilter($field, $cond);
//                    }
//                }
//            }
//        }
//        return $this;
//    }

    protected function _prepareColumns() {
        $this->addColumn('ticket_id', array(
            'header' => Mage::helper('helpdesk')->__('ID'),
            'align' => 'right',
            'width' => '40px',
            'index' => 'ticket_id',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('last_reply_time', array(
            'header' => Mage::helper('helpdesk')->__('Time Updated'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'last_reply_time',
            'width' => '150px',
            'gmtoffset' => true,
            'default' => ' ---- ',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('code_id', array(
            'header' => Mage::helper('helpdesk')->__('Ticket Id'),
            'align' => 'left',
            'index' => 'code_id',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('subject', array(
            'header' => Mage::helper('helpdesk')->__('Subject'),
            'align' => 'left',
            'index' => 'subject',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('sender', array(
            'header' => Mage::helper('helpdesk')->__('Sender'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'sender',
            'filter' => false,
            'sortable' => false,
        ));

        $departments = array();
        $collection = Mage::getModel('helpdesk/department')->getCollection();
        foreach ($collection as $department) {
            $departments[$department->getId()] = $department->getName();
        }

        $this->addColumn('department_id', array(
            'header' => Mage::helper('helpdesk')->__('Support Department'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'department_id',
            'type' => 'options',
            'options' => $departments,
            'filter' => false,
            'sortable' => false,
        ));

        $operators = array();
        $collection = Mage::getModel('helpdesk/member')->getCollection();
        foreach ($collection as $operator) {
            $operators[$operator->getId()] = $operator->getName();
        }

        $this->addColumn('member_id', array(
            'header' => Mage::helper('helpdesk')->__('Assigned Staff'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'member_id',
            'type' => 'options',
            'options' => $operators,
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('tag_id', array(
            'header' => Mage::helper('helpdesk')->__('Tags'),
            'index' => 'tag_id',
            'filter'    => false,
            'sortable'  => false,
            'renderer' => 'helpdesk/adminhtml_renderer_tags',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('helpdesk')->__('Status'),
            'align' => 'center',
            'width' => '140px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('helpdesk/config_source_status')->getOptionArray(),
            'frame_callback' => array($this, 'decorateStatus'),
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('priority', array(
            'header' => Mage::helper('helpdesk')->__('Priority'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'priority',
            'type' => 'options',
            'options' => Mage::getModel('helpdesk/config_source_priority')->getOptionArray(),
            'frame_callback' => array($this, 'decoratePriority'),
            'filter' => false,
            'sortable' => false,
        ));

//        $this->addColumn('abc', array(
//            'header' => Mage::helper('helpdesk')->__('Action'),
//            'width' => '60',
//            'type' => 'action',
//            'align' => 'center',
//            'getter' => 'getId',
//            'actions' => array(
//                array(
//                    'caption' => Mage::helper('helpdesk')->__('View'),
//                    'url' => array('base' => '*/*/view'),
//                    'field' => 'id'
//                )
//            ),
//            'filter' => false,
//            'sortable' => false,
//            'index' => 'stores',
//                //'is_system' => true,
//        ));

//		$this->addExportType('*/*/exportCsv', Mage::helper('helpdesk')->__('CSV'));
//		$this->addExportType('*/*/exportXml', Mage::helper('helpdesk')->__('XML'));

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

    public function getRowClass(Varien_Object $row) {
        if ($row->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN) {
            $flag = Mage::getModel('helpdesk/ticket')
                    ->missedTicket($row->getLastReplyTime(), $row->getStepReplyTime());
            if ($flag) {
                return 'unread';
            }
        }
    }

//    protected function _prepareMassaction() {
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
//            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
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
//            'url' => $this->getUrl('*/*/massPriority', array('_current' => true)),
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

    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array('id' => $row->getId(), 'action' => Mage::getSingleton('core/session')->getMenuSession()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/relatedTicketGrid', array('_current' => true));
    }

}
<?php
class MW_HelpDesk_Block_Adminhtml_Rules_Edit_Tab_Ticket_Applyticket extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ruleticketGrid');
        $this->setDefaultSort('ticket_id');
        $this->setUseAjax(true);
		/*
    	$collection = Mage::getModel('helpdesk/ruleticket')->getCollection()
        				->addFieldToFilter('rule_id',$this->getRequest()->getParam('id'));
	  	if(sizeof($collection) > 0){
	        	$this->setDefaultFilter(array('in_product_program'=>1));
	  	}
		*/
    }

	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_product_program') {
            $productIds = $this->_getSelectedOperators();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('ticket_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('ticket_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    // collection list member 
    protected function _prepareCollection()
    {
		$collection = Mage::getModel('helpdesk/ticket')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_product_program', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedOperators(),
                'align'             => 'center',
                'index'             => 'ticket_id'
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

//        $this->addColumn('quicknote', array(
//            'header' => Mage::helper('helpdesk')->__('Quick Note'),
//            'index' => 'quicknote',
//            //'renderer' => 'helpdesk/adminhtml_renderer_tags',
//        ));
        
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
        
        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            //'edit_only'         => !$this->_getProduct()->getId()
            'edit_only'         => true
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
        
		if($value == 5){
			 $this->getCollection()->addFieldToFilter('status', array(1, 4));
		}else{
			$this->getCollection()->addFieldToFilter('status', array($value));
		}
       
    }
    
    protected function _getSelectedOperators()
    {
        $products = array_keys($this->getSelectedAddOperators());
        return $products;
    }

    public function getSelectedAddOperators()
    { 
    	$collection = Mage::getModel('helpdesk/ruleticket')->getCollection()
        				->addFieldToFilter('rule_id',$this->getRequest()->getParam('id'));
        $products = array();
        
        foreach ($collection as $product) {
            $products[$product->getTicketId()] = $product->getTicketId();
        }
        return $products;
    }
    
	public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productGrid', array('_current'=>true));
    }
}

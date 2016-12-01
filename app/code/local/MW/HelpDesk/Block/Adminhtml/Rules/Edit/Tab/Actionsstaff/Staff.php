<?php
class MW_HelpDesk_Block_Adminhtml_Rules_Edit_Tab_Actionsstaff_Staff extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('memberGrid');
        $this->setDefaultSort('member_id');
        $this->setUseAjax(true);
    	$collection = Mage::getModel('helpdesk/rulemember')->getCollection()
        				->addFieldToFilter('rule_id',$this->getRequest()->getParam('id'));
	  	if(sizeof($collection) > 0){
	        	$this->setDefaultFilter(array('in_product_program'=>1));
	  	}
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
                $this->getCollection()->addFieldToFilter('member_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('member_id', array('nin'=>$productIds));
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
		$collection = Mage::getModel('helpdesk/member')->getCollection();
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
                'index'             => 'member_id'
            ));
     	
     	$this->addColumn('name_operator', array(
          'header'    => Mage::helper('helpdesk')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('email_operator', array(
          'header'    => Mage::helper('helpdesk')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      	));
      	
//		$departments = array();
//	  	$collection = Mage::getModel('helpdesk/department')->getCollection();
//	  	foreach ($collection as $department) {
//		 	$departments[$department->getId()] = $department->getName();
//	  	}
//	    $this->addColumn('department_id_operator', array(
//          'header'    => Mage::helper('helpdesk')->__('Moderator'),
//          'align'     =>'left',
//          'index'     => 'department_id',
//		  'type'      => 'options',
//          'options'   => $departments,
//        ));
        
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

    protected function _getSelectedOperators()
    {
        $products = array_keys($this->getSelectedAddOperators());
        return $products;
    }

    public function getSelectedAddOperators()
    { 
    	$collection = Mage::getModel('helpdesk/rulemember')->getCollection()
        				->addFieldToFilter('rule_id',$this->getRequest()->getParam('id'));
        $products = array();
        
        foreach ($collection as $product) {
            $products[$product->getMemberId()] = $product->getMemberId();
        }
        return $products;
    }
    
	public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

}

<?php
class MW_HelpDesk_Block_Adminhtml_Department_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('member_id');
        $this->setDefaultSort('member_id');
        $this->setUseAjax(true);
        
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_products'=>1));
        }

        //$collection = Mage::getModel('helpdesk/deme')->getCollection()
        //	->addFieldToFilter('department_id',$this->getRequest()->getParam('id'));
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
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
		$collection = Mage::getModel('helpdesk/member')->getCollection()
	  					->addFieldToFilter('active', array('eq' => 1));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_products',
            'values'            => $this->_getSelectedOperators(),
            'align'             => 'center',
            'index'             => 'member_id'
        ));
     	
     	$this->addColumn('name_operator', array(
          'header'    => Mage::helper('helpdesk')->__('Staff Name'),
          'align'     =>'left',
          'index'     => 'name',
      	));
      	
      	$this->addColumn('email_operator', array(
          'header'    => Mage::helper('helpdesk')->__('Staff Email Address'),
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
            'index'             => 'member_id',
            'editable'          => true,
            //'edit_only'         => !$this->_getProduct()->getId()
            'edit_only'         => true
        ));
          
        return parent::_prepareColumns();
    }

    protected function _getSelectedOperators()
    {
        
        $products = $this->getProductsAdd();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedAddOperators());
        }
        return $products;
    }

    public function getSelectedAddOperators()
    { 
    	
        $products = array();

        $collection = Mage::getModel('helpdesk/deme')->getCollection()
            ->addFieldToFilter('department_id',$this->getRequest()->getParam('id'));
        
        foreach ($collection as $product) {
            $products[$product->getMemberId()] = array('position' => $product->getMemberId());
        }
        return $products;
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/operatorgrid', array('_current'=>true));
    }
}

<?php

class Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_File extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_countTotals = true;
    
    const TWO_POINT_FOUR = 0.18;
    const FORTY_TWO = 3.15;
    const FIFTEEN = 1.125;
    const THIRTY = 2.25;
    const SEVEN = 0.525;
    const FOUR = 0.3;
    const TEN = 0.75;
    

    public function __construct() {

        parent::__construct();
        $this->setId('taxexcisereportGrid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setEmptyText('No Record Found.');

        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
       $this->setDefaultLimit(1000);
    }

    public function _prepareCollection() {
        $filters = Mage::getSingleton('core/session')->getReportFilterVars();
		
        if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
		$collection = Mage::getResourceModel('sales/order_collection');
        $collection->getSelect()->join(array('order_address' => sales_flat_order_address), 'order_address.parent_id = main_table.entity_id', array('order_address.region', 'order_address.region'));
        //$collection->getSelect()->join(array('sales_order' => sales_flat_order), 'sales_order.entity_id = main_table.order_id', array('sales_order.increment_id', 'sales_order.entity_id', 'sales_order.created_at', 'sales_order.shipping_amount','sales_order.subtotal','sales_order.status'));
        //$collection->getSelect()->join(array('sales_quote_item' => sales_flat_quote_item), 'main_table.quote_item_id = sales_quote_item.item_id', array('sales_quote_item.base_extra_tax_rule_amount'));
       
		}else{
        $collection = Mage::getResourceModel('sales/order_item_collection');
        $collection->getSelect()->join(array('order_address' => sales_flat_order_address), 'order_address.parent_id = main_table.order_id', array('order_address.region', 'order_address.region'));
        $collection->getSelect()->join(array('sales_order' => sales_flat_order), 'sales_order.entity_id = main_table.order_id', array('sales_order.increment_id', 'sales_order.entity_id', 'sales_order.created_at', 'sales_order.shipping_amount','sales_order.subtotal','sales_order.status'));
        $collection->getSelect()->join(array('sales_quote_item' => sales_flat_quote_item), 'main_table.quote_item_id = sales_quote_item.item_id', array('sales_quote_item.base_extra_tax_rule_amount'));
        }     
	   // $collection->addFieldToFilter('sales_order.extra_tax_rule_amount', array('neq' => 1));
        $collection->addFieldToFilter('order_address.address_type', 'shipping');
        if(isset($filters['region']) && $filters['region'] != 'Pennsylvania'){
	    $collection->addFieldToFilter('main_table.parent_item_id', array('null' => true));
        }
		if (!$this->getRequest()->getParam('filter')) {
            $collection->addFieldToFilter('sales_order.increment_id', 'NO-VALUE');
        }

        //$collection->printLogQuery(true);
        //exit();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getTotals() {
        $totals = new Varien_Object();
        $fields = array(
                // 'extra_tax_rule_amount' => 0,
        );
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field]+=$item->getData($field);
            }
        }
        //First column in the grid
        $fields['increment_id'] = 'Totals';
        //$fields['eliquid_ml'] = $this->totalEliquidml();
        $fields['eliquid_ml'] = $this->totalEliquidml();
        $fields['extra_tax_rule_amount'] = $this->calculateTotalExciseTax();
        
        $totals->setData($fields);
        return $totals;
    }
    
    
    

    protected function _prepareColumns() {
	 $filters = Mage::getSingleton('core/session')->getReportFilterVars();
		
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('taxexcisereport')->__('Order Id'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'increment_id',
            'sortable' => false,
        ));
		if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
        $this->addColumn('shipping_amount', array(
            'header' => Mage::helper('taxexcisereport')->__('Shipping Amount'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'shipping_amount',
			'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Format',
            'sortable' => false,
        ));
		}
		if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
        $this->addColumn('subtotal', array(
            'header' => Mage::helper('taxexcisereport')->__('Subtotal'),
            'align' => 'right',
            'width' => '50px',
			'type=' => 'number',
            'index' => 'subtotal',
			'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Format',
            'sortable' => false,
        ));
		}
		if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
        $this->addColumn('status', array(
            'header' => Mage::helper('taxexcisereport')->__('Status'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'status',
            'sortable' => false,
        ));
	    }
	   $filterDateIndex = 'sales_order.created_at';
       if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
        		$filterDateIndex = 'created_at';
		}
        $this->addColumn('created_at', array(
            'header' => Mage::helper('taxexcisereport')->__('Order Date'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'datetime',
            'format' => 'M/d/y',
            'index' => 'created_at',
            'filter_index' => $filterDateIndex,
            'sortable' => false,
        ));

        $this->addColumn('extra_tax_rule_amount', array(
            'header' => Mage::helper('taxexcisereport')->__('Excise Tax'),
            'align' => 'left',
            'index' => 'item_id',
            'renderer' => new Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Excisetax(true),
            'sortable' => false,
        ));
         if(isset($filters['region']) && $filters['region'] != 'Pennsylvania'){
        $this->addColumn('rate', array(
            'header' => Mage::helper('taxexcisereport')->__('Rate'),
            'align' => 'left',
            'index' => 'item_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Rate',
            'sortable' => false,
        ));
        }
        $this->addColumn('region', array(
            'header' => Mage::helper('taxexcisereport')->__('Region'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'region',
            'filter_index' => 'order_address.region',
            'sortable' => false,
        ));

        if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
         $this->addColumn('total_qty_ordered', array(
            'header' => Mage::helper('taxexcisereport')->__('Quantity'),
            'align' => 'left',
            'index' => 'total_qty_ordered',
			'type=' => 'number',
			'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Format',
            'sortable' => false,
        ));
		}else{
        $this->addColumn('qty_ordered', array(
            'header' => Mage::helper('taxexcisereport')->__('Quantity'),
            'align' => 'left',
            'index' => 'product_id',
			'type=' => 'number',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Quantity',
            'sortable' => false,
        ));
		}
		 if(isset($filters['region']) && $filters['region'] == 'Pennsylvania'){
          $this->addColumn('brand_name', array(
            'header' => Mage::helper('taxexcisereport')->__('BRAND NAME OF CIGARETTE LIQUID'),
            'align' => 'left',
            'index' => 'entity_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Brandnameorderitem',
            'sortable' => false,
           ));
		}else{
        $this->addColumn('brand_name', array(
            'header' => Mage::helper('taxexcisereport')->__('BRAND NAME OF CIGARETTE LIQUID'),
            'align' => 'left',
            'index' => 'item_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Brandname',
            'sortable' => false,
        ));
         }
        $this->addColumn('purchaser_name', array(
            'header' => Mage::helper('taxexcisereport')->__('NAME OF PURCHASER'),
            'align' => 'left',
            'index' => 'entity_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Purchaser',
            'sortable' => false,
        ));

        $this->addColumn('purchaser_address', array(
            'header' => Mage::helper('taxexcisereport')->__('Address Of Purchaser'),
            'align' => 'left',
            'index' => 'entity_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Purchaseraddress',
            'sortable' => false,
        ));
         if(isset($filters['region']) && $filters['region'] != 'Pennsylvania'){
       
        $this->addColumn('eliquid_ml', array(
            'header' => Mage::helper('taxexcisereport')->__('NUMBER OF MILLILITERS SOLD'),
            'align' => 'left',
            'index' => 'item_id',
            'renderer' => new Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Taxexcisereport(true),
            'sortable' => false,
        ));
    
        $this->addColumn('price', array(
            'header' => Mage::helper('taxexcisereport')->__('COST OF E-CIGARETTE LIQUIDS SOLD'),
            'align' => 'left',
            'index' => 'product_id',
            'renderer' => 'Halox_Taxexcisereport_Block_Adminhtml_Taxexcisereport_Renderer_Price',
            'sortable' => false,
        ));
		}

        
        $this->addExportType('*/*/exportCsv/', Mage::helper('taxexcisereport')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('taxexcisereport')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return "javascript&colon;void(0)";
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/index', array('_current' => true));
    }

    public function totalEliquidml() {
        $totaleliquidml = 0;
        foreach ($this->getCollection() as $row) {
            
            if($row->getData('product_type') == 'configurable'){
                $sku = $row->getData('sku');
                $_product = Mage::getModel('catalog/product')
                    ->loadByAttribute('sku',$sku);
                $eMl = $_product->getAttributeText('eliquid_ml');
                if (isset($eMl) && !empty($eMl)) {
                    $totaleliquidml += $eMl;
                }
             }else{
            
            $productId = $row->getData('product_id');
            $_product = Mage::getModel('catalog/product')
                    ->load($productId);
            $eMl = $_product->getAttributeText('eliquid_ml');
            if (isset($eMl) && !empty($eMl)) {
                $totaleliquidml += $eMl;
            }
            
            
            }
            
        }
        return $totaleliquidml;
    }
    
     public function calculateTotalExciseTax() {
        $totalExciseTax = 0;
        foreach ($this->getCollection() as $row) {
		    $state = $row->getData('region');
			if($state == "Pennsylvania"){
			   $exciseTax = $row->getdata('base_extra_tax_rule_amount');
			   //return $exciseTax;
			   $totalExciseTax += $exciseTax;
	    
			}else{
            $product_type = $row->getData('product_type');
            if($product_type == 'configurable'){
                $sku = $row->getData('sku');
                $_product = Mage::getModel('catalog/product')
                    ->loadByAttribute('sku',$sku);     
                $eMl = $_product->getAttributeText('eliquid_ml');
             }else{
              $productId = $row->getData('product_id');
              $_product = Mage::getModel('catalog/product')
                    ->load($productId);
              $eMl = $_product->getAttributeText('eliquid_ml');
            }
            
            if (isset($eMl) && !empty($eMl)) {
              if ($eMl == 2.4) {
                    $rate = self::TWO_POINT_FOUR;
                } elseif ($eMl == 42) {
                    $rate = self::FORTY_TWO;
                } elseif ($eMl == 15) {
                    $rate = self::FIFTEEN;
                } elseif ($eMl == 30) {
                    $rate = self::THIRTY;
                } elseif ($eMl == 7) {
                    $rate = self::SEVEN;
                } elseif ($eMl == 4) {
                    $rate = self::FOUR;
                } elseif ($eMl == 10) {
                    $rate = self::TEN;
                } else {
                    $rate = 0;
                }
				
            $quantity = $row ->getdata('qty_ordered');
            $extax = ($rate * $quantity);
            if(isset($extax)){
                    $exciseTax = number_format((float)$extax, 2, '.', '');
                    $totalExciseTax += $exciseTax;
               }	
            }
          }
        }
        return round($totalExciseTax,2);
    }

}

<?php

class Halo_Deals_Block_Adminhtml_Deals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

        public function __construct()
        {
                parent::__construct();
                $this->setId("dealsGrid");
                $this->setDefaultSort("id");
                $this->setDefaultDir("DESC");
                $this->setSaveParametersInSession(true);
        }

        protected function _prepareCollection()
        {
                $collection = Mage::getModel("deals/deals")->getCollection();
                $this->setCollection($collection);
                return parent::_prepareCollection();
        }
        protected function _prepareColumns()
        {
                $this->addColumn("id", array(
                "header" => Mage::helper("deals")->__("ID"),
                "align" =>"left",
                "width" => "10px",
                "type" => "number",
                "index" => "id",
                ));
                
                        $this->addColumn('deal_name', array(
                        'header' => Mage::helper('deals')->__('Deal Name'),
                        'index' => 'deal_name',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray0(),                
                        ));
                        $this->addColumn("voucher", array(
                        "header" => Mage::helper("deals")->__("Voucher"),
                        "index" => "voucher",
                        ));
                        $this->addColumn('type_of_kit', array(
                        'header' => Mage::helper('deals')->__('Type Of Kit Options'),
                        'index' => 'type_of_kit',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray1(),                
                        ));
                        
                        $this->addColumn('sample_pack', array(
                        'header' => Mage::helper('deals')->__('Sample Pack'),
                        'index' => 'sample_pack',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray8(),                
                        ));
                        
                        $this->addColumn('battery_color', array(
                        'header' => Mage::helper('deals')->__('Battery Color'),
                        'index' => 'battery_color',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray2(),                
                        ));
                        
                        $this->addColumn('flavor_option_1', array(
                        'header' => Mage::helper('deals')->__('e-Liquid Flavor Options (Bottle 1)'),
                        'index' => 'flavor_option_1',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray3(),                
                        ));
                        
                        $this->addColumn('flavor_option_2', array(
                        'header' => Mage::helper('deals')->__('e-Liquid Flavor Options (Bottle 2)'),
                        'index' => 'flavor_option_2',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray4(),                
                        ));
                        $this->addColumn('fax', array(
                        'header' => Mage::helper('deals')->__('e-Liquid Nicotine Level'),
                        'index' => 'fax',
                        'type' => 'options',
                        'options'=>Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray5(),                
                        ));
                        
                $this->addColumn("first_name", array(
                "header" => Mage::helper("deals")->__("First Name"),
                "index" => "first_name",
                ));
                $this->addColumn("telephone", array(
                "header" => Mage::helper("deals")->__("Last Name"),
                "index" => "telephone",
                ));
                $this->addColumn("address", array(
                "header" => Mage::helper("deals")->__("Address Line 1"),
                "index" => "address",
                ));
                $this->addColumn("company", array(
                "header" => Mage::helper("deals")->__("Address Line 2"),
                "index" => "company",
                ));
                $this->addColumn("city", array(
                "header" => Mage::helper("deals")->__("City"),
                "index" => "city",
                ));
                $this->addColumn("state", array(
                "header" => Mage::helper("deals")->__("State"),
                "index" => "state",
                ));
                $this->addColumn("zip", array(
                "header" => Mage::helper("deals")->__("Postal Code"),
                "index" => "zip",
                ));
                $this->addColumn("country", array(
                "header" => Mage::helper("deals")->__("Country"),
                "index" => "country",
                ));
                $this->addColumn("last_name", array(
                "header" => Mage::helper("deals")->__("Email"),
                "index" => "last_name",
                ));
               /* $this->addColumn("fax", array(
                "header" => Mage::helper("deals")->__("Fax"),
                "index" => "fax",
                ));*/
                
                
                
            $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
            $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

                return parent::_prepareColumns();
        }

        public function getRowUrl($row)
        {
              // return $this->getUrl("*/*/edit", array("id" => $row->getId()));
        }


        
        protected function _prepareMassaction()
        {
            $this->setMassactionIdField('id');
            $this->getMassactionBlock()->setFormFieldName('ids');
            $this->getMassactionBlock()->setUseSelectAll(true);
            $this->getMassactionBlock()->addItem('remove_deals', array(
                     'label'=> Mage::helper('deals')->__('Remove Deals'),
                     'url'  => $this->getUrl('*/adminhtml_deals/massRemove'),
                     'confirm' => Mage::helper('deals')->__('Are you sure?')
                ));
            return $this;
        }
            
        static public function getOptionArray0()
        {
            $data_array=array(); 
            $data_array[0]='Groupon';
            $data_array[1]='Living Social';
            $data_array[2]='e-Liquid Sample Pack';
            return($data_array);
        }
        static public function getValueArray0()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray0() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        
        static public function getOptionArray1()
        {
            $data_array=array(); 
            $data_array[0]='N/A';
            $data_array[1]='Tobacco - Tribeca';
            $data_array[2]='Menthol - Mystic';
            $data_array[3]='Tobacco - Tribeca';
            $data_array[4]='Menthol - SubZero';
            return($data_array);
        }
        static public function getValueArray1()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        
        static public function getOptionArray2()
        {
            $data_array=array();
            $data_array[0]='N/A'; 
            $data_array[1]='Black';
            $data_array[2]='Titanium';
            $data_array[3]='Red';
            $data_array[4]='Blue';
            $data_array[5]='Iridescence';
            return($data_array);
        }
        static public function getValueArray2()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        
        static public function getOptionArray3()
        {
            $data_array=array(); 
            $data_array[0]='N/A';
            $data_array[1]='Tribeca (sweet tobacco)';
            $data_array[2]='Torque 56 (classic tobacco)';
            $data_array[3]='Malibu (pina colada)';
            $data_array[4]='SubZero (X strength menthol)';
            $data_array[5]='Belgian Cocoa (chocolate)';
            $data_array[6]="Kringle's Curse (peppermint)";
            $data_array[7]='Tobacco - Tribeca';
            $data_array[8]='Menthol - SubZero';
            return($data_array);
        }
        static public function getValueArray3()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        
        static public function getOptionArray4()
        {
            $data_array=array(); 
            $data_array[0]='N/A';
            $data_array[1]='Tribeca (sweet tobacco)';
            $data_array[2]='Torque 56 (classic tobacco)';
            $data_array[3]='Malibu (pina colada)';
            $data_array[4]='SubZero (X strength menthol)';
            $data_array[5]='Belgian Cocoa (chocolate)';
            $data_array[6]="Kringle's Curse (peppermint)";
            $data_array[7]='Tobacco - Tribeca';
            $data_array[8]='Menthol - SubZero';
            return($data_array);
        }
        static public function getValueArray4()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        static public function getOptionArray5()
        {
            $data_array=array(); 
            $data_array[0]='N/A';
            $data_array[1]='00 mg/ml  - No Nicotine';
            $data_array[2]='06 mg/ml - Low Nicotine';
            $data_array[3]='12 mg/ml - Medium Nicotine';
            $data_array[4]='18 mg/ml - High Nicotine';
            $data_array[5]='24 mg/ml - XHigh Nicotine';
            return($data_array);
        }
        static public function getValueArray5()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        static public function getOptionArray8()
        {
            $data_array=array(); 
            $data_array[0]='N/A';
            $data_array[1]='Caf&eacute; Sample Pack';
            $data_array[2]='Tobacco/Menthol Variety Sample Pack';
            $data_array[3]='Harvest Sample Pack';
            return($data_array);
        }
        static public function getValueArray8()
        {
            $data_array=array();
            foreach(Halo_Deals_Block_Adminhtml_Deals_Grid::getOptionArray8() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);        
            }
            return($data_array);

        }
        

}
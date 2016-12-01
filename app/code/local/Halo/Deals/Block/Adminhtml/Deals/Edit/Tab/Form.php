<?php
class Halo_Deals_Block_Adminhtml_Deals_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("deals_form", array("legend"=>Mage::helper("deals")->__("Item information")));

								
						 $fieldset->addField('deal_name', 'select', array(
						'label'     => Mage::helper('deals')->__('Deal Name'),
						'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray0(),
						'name' => 'deal_name',					
						"class" => "required-entry",
						"required" => true,
						));	
                        $fieldset->addField("voucher", "text", array(
                        "label" => Mage::helper("deals")->__("Voucher"),
                        "name" => "voucher",
                        "class" => "required-entry",
                        "required" => true,
                        ));			
						$fieldset->addField('type_of_kit', 'select', array(
						'label'     => Mage::helper('deals')->__('Type Of Kit'),
						'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray1(),
						'name' => 'type_of_kit',					
						"class" => "required-entry",
						"required" => true,
						));				
                        $fieldset->addField('sample_pack', 'select', array(
                        'label'     => Mage::helper('deals')->__('Sample Pack'),
                        'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray8(),
                        'name' => 'sample_pack',                    
                        "class" => "required-entry",
                        "required" => true,
                        ));
						 $fieldset->addField('battery_color', 'select', array(
						'label'     => Mage::helper('deals')->__('battery Color'),
						'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray2(),
						'name' => 'battery_color',					
						"class" => "required-entry",
						"required" => true,
						));				
						 $fieldset->addField('flavor_option_1', 'select', array(
						'label'     => Mage::helper('deals')->__('Flavor Option 1'),
						'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray3(),
						'name' => 'flavor_option_1',					
						"class" => "required-entry",
						"required" => true,
						));				
						 $fieldset->addField('flavor_option_2', 'select', array(
						'label'     => Mage::helper('deals')->__('Flavor Option 2'),
						'values'   => Halo_Deals_Block_Adminhtml_Deals_Grid::getValueArray4(),
						'name' => 'flavor_option_2',					
						"class" => "required-entry",
						"required" => true,
						));
						$fieldset->addField("first_name", "text", array(
						"label" => Mage::helper("deals")->__("First Name"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "first_name",
						));
					
						$fieldset->addField("address", "text", array(
						"label" => Mage::helper("deals")->__("Address Line 2"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "address",
						));
					
                        $fieldset->addField("company", "text", array(
                        "label" => Mage::helper("deals")->__("Address Line 2"),                    
                        "class" => "required-entry",
                        "required" => true,
                        "name" => "company",
                        ));
                        
						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("deals")->__("City"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "city",
						));
					
						$fieldset->addField("state", "text", array(
						"label" => Mage::helper("deals")->__("State"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "state",
						));
					
						$fieldset->addField("zip", "text", array(
						"label" => Mage::helper("deals")->__("Postal Code"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "zip",
						));
					
						$fieldset->addField("country", "text", array(
						"label" => Mage::helper("deals")->__("Country"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "country",
						));
					
                        $fieldset->addField("last_name", "text", array(
                        "label" => Mage::helper("deals")->__("Email"),                    
                        "class" => "required-entry",
                        "required" => true,
                        "name" => "last_name",
                        ));
                        
						$fieldset->addField("telephone", "text", array(
						"label" => Mage::helper("deals")->__("Telephone"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "telephone",
						));
					
						$fieldset->addField("fax", "text", array(
						"label" => Mage::helper("deals")->__("Fax"),
						"name" => "fax",
						));
					
						
					

				if (Mage::getSingleton("adminhtml/session")->getDealsData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getDealsData());
					Mage::getSingleton("adminhtml/session")->setDealsData(null);
				} 
				elseif(Mage::registry("deals_data")) {
				    $form->setValues(Mage::registry("deals_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_GroupedProductsPro
 * @copyright  Copyright (c) 2011 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
/**
 * Observer model
 */
class Crius_GroupedProductsPro_Model_Observer
{
    /**
     * Add fields to attribute editor to toggle the attribute's visibility and sort order in the grouped table
     *
     * @param Varien_Event_Observer $observer
     */
	public function addGroupedTableAttributeFields($observer)
	{
		$fieldset = $observer->getForm()->getElement('base_fieldset');
		$attribute = $observer->getAttribute();
		
		if ($this->_getIsAttributeAllowedInGroupedTable($attribute->getAttributeCode())) {
		    // Visible in grouped table yes/no
			$fieldset->addField('is_visible_in_grouped_table', 'select', array(
	            'name'      => 'is_visible_in_grouped_table',
	            'label'     => Mage::helper('groupedproductspro')->__('Visible in Grouped Product Table'),
	            'title'     => Mage::helper('groupedproductspro')->__('Visible in Grouped Product Table'),
	            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
	        ));
	        
	        // Sort order in grouped table
	        $fieldset->addField('grouped_table_sort_order', 'text', array(
                'name'      => 'grouped_table_sort_order',
                'label'     => Mage::helper('groupedproductspro')->__('Grouped Product Table Sort Order'),
                'title'     => Mage::helper('groupedproductspro')->__('Grouped Product Table Sort Order'),
                'class'     => 'validate-digits',
            ));
		}
	}
	
	/**
	 * Check if attribute is valid for use in grouped table
	 *
	 * @param string $attributeCode
	 * @return boolean
	 */
	protected function _getIsAttributeAllowedInGroupedTable($attributecode)
	{
	    // Exclude "price_view", because we use the "price" attribute for displaying prices
		return !in_array($attributecode, array('price_view'));
	}
}
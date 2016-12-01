<?php

class Halox_ExpediteShipping_Model_Adminhtml_System_Config_Source_MarkupType
{

	const MARKUP_TYPE_FIXED = 1;
	const MARKUP_TYPE_PERCENTAGE = 2;

	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
            	'value' => static::MARKUP_TYPE_FIXED, 
            	'label'=>Mage::helper('adminhtml')->__('Fixed')
            ),
            array(
            	'value' => static::MARKUP_TYPE_PERCENTAGE, 
            	'label'=>Mage::helper('adminhtml')->__('Percentage')
            )
        );
    }

} 
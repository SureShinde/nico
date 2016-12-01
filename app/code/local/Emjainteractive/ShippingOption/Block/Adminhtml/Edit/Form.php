<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

class Emjainteractive_ShippingOption_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('edit_form');
        $this->setTitle(Mage::helper('emjainteractive_shippingoption')->__('Option Information'));
    }
	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('option_form', array('legend'=>Mage::helper('emjainteractive_shippingoption')->__('Option information')));

        $fieldset->addField('label', 'text', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Label'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'label',
        ));
        
        $fieldset->addField('code', 'text', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'code',
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('emjainteractive_shippingoption')->__('(eg: the_code_123') . '</small></p>',
        ));
        
        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'type',
            'onchange'  => 'checkOptionType()',
            'values'    => array(
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_TEXT,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Text Input'),
                ),
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_TEXTAREA,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Text Area'),
                ),
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_CHECKBOX,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Checkbox'),
                ),
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_RADIO,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Radio'),
                ),
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_SELECT,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Select'),
                ),
            ),
        ));
        
        
        $fieldset->addField('is_required', 'select', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Is Required'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'is_required',
            'values'    => array(
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::IS_REQUIRED,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('Yes'),
                ),
                array(
                    'value'     => Emjainteractive_ShippingOption_Model_Option::NOT_REQUIRED,
                    'label'     => Mage::helper('emjainteractive_shippingoption')->__('No'),
                ),
            ),
        ));
        
        
        $fieldset->addField('default_value', 'text', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Default Value'),
            'name'      => 'default_value',
        ));
        
        $fieldset->addField('options', 'textarea', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Options (Comma-delimited)'),
            'name'      => 'options',
        ));

        $fieldset->addField('apply_to', 'hidden', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Apply To'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'apply_to',
            'value'     => 'umosaco',
        ));        
        
        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('emjainteractive_shippingoption')->__('Sort Order'),
            'name'      => 'sort_order',
        ));
        
        if( Mage::getSingleton('adminhtml/session')->getOptionData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getOptionData());
            Mage::getSingleton('adminhtml/session')->setOptionData(null);
        } elseif( Mage::registry('option_data') ) {
            $form->setValues(Mage::registry('option_data')->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
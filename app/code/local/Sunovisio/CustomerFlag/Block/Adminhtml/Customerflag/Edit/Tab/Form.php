<?php

class Sunovisio_CustomerFlag_Block_Adminhtml_Customerflag_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('customerflag_form', array('legend' => Mage::helper('customerflag')->__('Item information')));

        $fieldset->addField('picture', 'image', array(
            'label' => Mage::helper('customerflag')->__('Icon'),
            'required' => false,
            'name' => 'picture',
        ));

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('customerflag')->__('Code'),
            'required' => true,
            'name' => 'code',
        ));

        $fieldset->addField('label', 'text', array(
            'label' => Mage::helper('customerflag')->__('Label'),
            'name' => 'label',
            'required' => true
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('customerflag')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('customerflag')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('customerflag')->__('Disabled'),
                ),
            ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getCustomerflagData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomerflagData());
            Mage::getSingleton('adminhtml/session')->setCustomerflagData(null);
        } elseif (Mage::registry('customerflag_data')) {
            $form->setValues(Mage::registry('customerflag_data')->getData());
        }
        return parent::_prepareForm();
    }

}
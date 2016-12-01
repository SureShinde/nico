<?php

class Halox_AgeVerification_Block_Adminhtml_Ageverification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('ageverification_form', array('legend' => Mage::helper('ageverification')->__('Item information')));

        $country = $fieldset->addField('country', 'select', array(
            'name' => 'country',
            'label' => Mage::helper('ageverification')->__('Country'),
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
            'class' => 'required-entry',
            'required' => true,
            'onchange' => 'getstate(this)',
        ));


        $fieldset->addField('state', 'select', array(
            'name' => 'state',
            'label' => Mage::helper('ageverification')->__('State'),
            'class' => 'required-entry',
            'required' => true,
            'values' => Mage::helper('ageverification')->getStates('US'),
        ));

        /*
         * Add Ajax to the Country select box html output
         */
        $country->setAfterElementHtml("<script type=\"text/javascript\">
            function getstate(selectElement){
                var reloadurl = '" . $this->getUrl('*/ageverification/getState') . "country/' + selectElement.value;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (stateform) {
                        $('state').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('state').update(stateform.responseText);
                    }
                });
            }
        </script>");

        $fieldset->addField('age', 'text', array(
            'label' => Mage::helper('ageverification')->__('Age'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'age',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('ageverification')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('ageverification')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('ageverification')->__('Disabled'),
                ),
            ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getVerificationData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVerificationData());
            Mage::getSingleton('adminhtml/session')->setVerificationData(null);
        } elseif (Mage::registry('ageverification_data')) {
            $form->setValues(Mage::registry('ageverification_data')->getData());
        }
        return parent::_prepareForm();
    }

}

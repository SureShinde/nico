<?php
/**
 * Extend admin page for editing cms pages
 *
 */
class Halox_Fixes_Model_Observer_Cms
{
    /**
     * Add og & tc description fields to form
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function prepareForm(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();               
        
        $fieldset = $form->getElement('meta_fieldset');

        $fieldset->addField('og_description', 'textarea', array(
            'name' => 'og_description',
            'label' => 'OG Description',
            'title' => 'OG Description'
        ));
         $fieldset->addField('tc_description', 'textarea', array(
            'name' => 'tc_description',
            'label' => 'TC Description',
            'title' => 'TC Description'
        ));
    }
    
    /**
     * Shortcut to getRequest
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
}
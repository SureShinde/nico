<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.9.2
 * @license:     QvHpbEc0oOzK3qo2hlmyDcWVqjqnkaNA9m9UogV4wV
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlPromoQuoteEditTabLabels extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels
{
    protected function _prepareForm()
    {
        $rule = Mage::registry('current_promo_quote_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('default_label_fieldset', array(
            'legend' => Mage::helper('salesrule')->__('Default Label')
        ));
        $labels = $rule->getStoreLabels();
        $fieldset->addField('store_default_label', 'text', array(
            'name'      => 'store_labels[0]',
            'required'  => false,
            'label'     => Mage::helper('salesrule')->__('Default Rule Label for All Store Views'),
            'value'     => isset($labels[0]) ? $labels[0] : '',
        ));

        $fieldset = $form->addFieldset('store_labels_fieldset', array(
            'legend'       => Mage::helper('salesrule')->__('Store View Specific Labels'),
            'table_class'  => 'form-list stores-tree',
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset');
        $fieldset->setRenderer($renderer);
        /* <<< AITOC_FIX */
        $role = Mage::getSingleton('aitpermissions/role');
        $websiteIds = $role->getAllowedWebsiteIds();
        $storeGroupIds = $role->getAllowedStoreIds();
        $storeIds = $role->getAllowedStoreviewIds();
        /* >>> AITOC_FIX */
        foreach (Mage::app()->getWebsites() as $website) {
            /* <<< AITOC_FIX */
            if($role->isPermissionsEnabled() && !in_array($website->getId(), $websiteIds))
            {
                continue;
            }
            /* >>> AITOC_FIX */
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                /* <<< AITOC_FIX */
                if($role->isPermissionsEnabled() && !in_array($group->getId(), $storeGroupIds))
                {
                    continue;
                }
                /* >>> AITOC_FIX */
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    /* <<< AITOC_FIX */
                    if($role->isPermissionsEnabled() && !in_array($store->getId(), $storeIds))
                    {
                        continue;
                    }
                    /* >>> AITOC_FIX */
                    $fieldset->addField("s_{$store->getId()}", 'text', array(
                        'name'      => 'store_labels['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                        'fieldset_html_class' => 'store',
                    ));
                }
            }
        }


        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        return $this;
    }
}
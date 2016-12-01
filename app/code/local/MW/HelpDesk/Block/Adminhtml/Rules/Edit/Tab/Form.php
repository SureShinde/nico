<?php

class MW_HelpDesk_Block_Adminhtml_Rules_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $form->setHtmlIdPrefix('rule_');
	  $model =  Mage::registry('rule_data');
      
	  //$fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalogrule')->__('General Information')));
 	  $fieldset = $form->addFieldset('helpdesk_form', array('legend'=>Mage::helper('helpdesk')->__('Detail Information')));

  	  $fieldset->addField('auto_apply', 'hidden', array(
            'name' => 'auto_apply',
        ));

      if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }
 	  
        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('helpdesk')->__('Rule Name'),
            'title' => Mage::helper('helpdesk')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('helpdesk')->__('Description'),
            'title' => Mage::helper('helpdesk')->__('Description'),
            'style' => 'width: 98%; height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('helpdesk')->__('Status'),
            'title'     => Mage::helper('helpdesk')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('helpdesk')->__('Enabled'),
                '2' => Mage::helper('helpdesk')->__('Disabled'),
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'      => 'website_ids[]',
                'label'     => Mage::helper('catalogrule')->__('Websites'),
                'title'     => Mage::helper('catalogrule')->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name'      => 'website_ids[]',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $model->setWebsiteIds(Mage::app()->getStore(true)->getWebsiteId());
        }
        
        
        //$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
			'required' => true,
            'label'  => Mage::helper('helpdesk')->__('From Date'),
            'title'  => Mage::helper('helpdesk')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'yyyy-MM-dd',
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
			'required' => true,
            'label'  => Mage::helper('helpdesk')->__('To Date'),
            'title'  => Mage::helper('helpdesk')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'yyyy-MM-dd',
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('helpdesk')->__('Priority'),
        ));
        
   	  	if ( Mage::getSingleton('adminhtml/session')->getRuleData() )
      	{
	          $form->setValues(Mage::getSingleton('adminhtml/session')->getRuleData());
	          Mage::getSingleton('adminhtml/session')->getRuleData(null);
      	} elseif ( Mage::registry('rule_data') ) {
          	$form->setValues(Mage::registry('rule_data')->getData());
      	}
      return parent::_prepareForm();
  }
}
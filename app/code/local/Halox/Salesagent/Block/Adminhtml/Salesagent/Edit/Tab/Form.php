<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Salesagent edit form tab
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Block_Adminhtml_Salesagent_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Salesagent_Salesagent_Block_Adminhtml_Adbanner_Edit_Tab_Form
          */
    protected function _prepareForm(){ 
	  $formData = Mage::getSingleton('adminhtml/session')->getFormData();
      if ($formData)
        {
            $data = $formData;
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } else
        {
            $data = array();
        }
		//$data = array();
 
        $form = new Varien_Data_Form(array(
                'id' => 'agentdata',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
        $form->setHtmlIdPrefix('agentdata');
        $form->setFieldNameSuffix('agentdata');
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('agentdata', array(
             'legend' =>Mage::helper('halox_salesagent')->__('Add Agent Information')
        ));
 
        $fieldset->addField('name', 'text', array(
             'label'     => Mage::helper('halox_salesagent')->__('Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'name',
             'note'     => Mage::helper('halox_salesagent')->__('The name of the Agent.'),
        ));
        $fieldset->addField('email', 'text', array(
             'label'     => Mage::helper('halox_salesagent')->__('Email'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'email',
        ));
        $fieldset->addField('phone', 'text', array(
             'label'     => Mage::helper('halox_salesagent')->__('Phone'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'phone',
			 
        ));
        
        $fieldset->addField('description', 'textarea', array(
             'label'     => Mage::helper('halox_salesagent')->__('Description'),
             'class'     => 'none',
             'required'  => true,
             'name'      => 'description',
        ));
		
		$fieldset->addType('image', 'Halox_Salesagent_Block_Adminhtml_Salesagent_Helper_Image');
		
		$fieldset->addField('image', 'image', array(
            'label' => Mage::helper('halox_salesagent')->__('Profile Image'),
            'name'  => 'image',

        ));
		 $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('halox_salesagent')->__('Status'),
            'name'  => 'status',
			'value' => 1,
            'values'=> array(
			    array(
                    'value' => 0,
                    'label' => Mage::helper('halox_salesagent')->__('Disabled'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('halox_salesagent')->__('Enabled'),
                ),
               
            ),
        ));
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}

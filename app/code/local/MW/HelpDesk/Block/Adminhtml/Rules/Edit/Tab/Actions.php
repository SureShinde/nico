<?php
class MW_Helpdesk_Block_Adminhtml_Rules_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $form->setHtmlIdPrefix('rule_');
	  
      //*** Form 2:
	  
 	  $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('helpdesk')->__('Rule Actions Information')));

 	  $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('helpdesk')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('helpdesk')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('helpdesk')->__('Yes'),
                '0' => Mage::helper('helpdesk')->__('No'),
            ),
      ));
 	  
      $fieldset->addField('ac_status', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Set Status To'),
          'name'      => 'ac_status',
          'values'    => Mage::getModel('helpdesk/config_source_status')->getOptionArrStatus()
      ));
      
      $fieldset->addField('ac_priority', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Priority'),
       	  'title'     => Mage::helper('helpdesk')->__('Priority'),
          'name'      => 'ac_priority',
		  'values'    => Mage::getModel('helpdesk/config_source_priority')->getOptionArrPriority()
      ));
      
      $departments = array();
      $departments[0] = '-- Please select department --';
	  $collection = Mage::getModel('helpdesk/department')->getCollection()
	  				->addFieldToFilter('active', array('eq' => 1));
	  foreach ($collection as $department) {
		 	$departments[$department->getId()] = $department->getName();
	  }
      $fieldset->addField('ac_department_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Assign To Department'),
          'name'      => 'ac_department_id',
          'values'    => $departments,
      	  //'note'	=> 'This ticket will be sent to Moderator of chosen department',
      ));
       
//      $fieldset->addField('operator', 'text', array(
//          'label'     => Mage::helper('helpdesk')->__('Assign to Staff'),
//      	  'note'	=> 'Staff Email',
//          'name'      => 'operator',
//      ));

      $members = array();
      $members[0] = '-- Please select staff --';
	  $collection = Mage::getModel('helpdesk/member')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  
				//Zend_debug::dump($collection->getData());die;
	  foreach ($collection as $member) {
		 	$members[$member->getId()] = $member->getName() . '  [ ' . $member->getEmail() . ' ]';
	  }
      $fieldset->addField('ac_member_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Assign to Staff'),
          'name'      => 'ac_member_id',
          'values'    => $members,
      	  //'note'	=> 'This ticket will be sent to Moderator of chosen department',
      ));
        
      $fieldset->addField('ac_tags_name', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Add Tags'),
          'name'      => 'ac_tags_name',
      ));
	  
	  $fieldset->addField('remove_tags_name', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Remove Tags'),
          'name'      => 'remove_tags_name',
      ));
      
      $templates = array();
      $templates[0] = '-- Please select template --';
	  $collection = Mage::getModel('helpdesk/template')->getCollection()
				->addFieldToFilter('active', array('eq' => 1));  
				//Zend_debug::dump($collection->getData());die;
	  foreach ($collection as $template) {
		 	$templates[$template->getId()] = $template->getTitle();
	  }
      $fieldset->addField('ac_template_id', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Reply With'),
          'name'      => 'ac_template_id',
          'values'    => $templates,
      	  //'note'	=> 'This ticket will be sent to Moderator of chosen department',
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
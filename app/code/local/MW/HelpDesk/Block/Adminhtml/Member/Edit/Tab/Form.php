<?php
 
class MW_HelpDesk_Block_Adminhtml_Member_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('member_form', array('legend'=>Mage::helper('helpdesk')->__('Staff Detail')));  
         
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Staff Name'),
      	  'title' => Mage::helper('helpdesk')->__('Staff Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
	  
	  $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('helpdesk')->__('Email Address'),
      	  'title' => Mage::helper('helpdesk')->__('Email Address'),
          'class' => 'required-entry validate-email',
          'required'  => true,
          'name'      => 'email',
      ));
	  
	  $fieldset->addField('active', 'select', array(
          'label'     => Mage::helper('helpdesk')->__('Active'),
          'name'      => 'active',
	      'note'	  => Mage::helper('helpdesk')->__('Assign staff to departments under "Manage Departments"'),
          'options'   => array(
          	'1' => Mage::helper('helpdesk')->__('Yes'),
            '2' => Mage::helper('helpdesk')->__('No'))
      ));
      
      
//      if (Mage::registry('member_data')) {
//	        $collection = Mage::getModel('helpdesk/member')->getCollection()
//	        				->addFieldToFilter('main_table.member_id', Mage::registry('member_data')->getId());
//	    	$collection->getSelect()->join('mw_departments',
//	                   		'main_table.member_id=mw_departments.member_id',
//	                    	array('moderator' =>'mw_departments.name') 
//	        );  echo $collection->getSelect();die(); //Zend_Debug::Dump($collection);die();
//	        $moderator = '';
//	        foreach ($collection as $operator ) {
//	        	$moderator = $operator->getModerator();
//	        }
//	      	$fieldset->addField('moderator', 'note', array(
//	          'label' => Mage::helper('helpdesk')->__('Department'),
//	          'title' => Mage::helper('helpdesk')->__('Department'),	
//	          'text'  => $moderator
//	      	));
//      }
     
      if( Mage::getSingleton('adminhtml/session')->getMemberData() ) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMemberData());
          Mage::getSingleton('adminhtml/session')->setMemberData(null);
      } elseif ( Mage::registry('member_data') ) {
          $form->setValues(Mage::registry('member_data')->getData());
      }
      
      return parent::_prepareForm();
  }
}
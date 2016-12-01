<?php
class MW_Helpdesk_Block_Adminhtml_Rules_Edit_Tab_Applyto
    extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
    {
        parent::__construct();
    }
/*
    protected function _prepareForm()
    {
    	$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('helpdesk/rules')->load($id);
		
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('helpdesk')->__('Apply rule for tickets')));

        $fieldset->addField('is_created', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('When ticket created'),
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_created',
		));
		
		$fieldset->addField('is_updated', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('When ticket updated'),
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_updated',
		));
		
//		$fieldset->addField('my_label', 'label', array(
//		    'label'     => Mage::helper('helpdesk')->__('MENUAL TICKET'),
//		    'name'      => 'my_label',
//		));
        
		$form->setValues($model->getData());
	
        $this->setForm($form);
    	if ( Mage::getSingleton('adminhtml/session')->getRuleData() )
      	{
	          $form->setValues(Mage::getSingleton('adminhtml/session')->getRuleData());
	          Mage::getSingleton('adminhtml/session')->getRuleData(null);
      	} elseif ( Mage::registry('rule_data') ) {
          	$form->setValues(Mage::registry('rule_data')->getData());
      	}
      	$fieldset1 = $form->addFieldset('action_fieldset1', array('legend'=>Mage::helper('helpdesk')->__('Menual ticket(s)')));

		$fieldset1 = $form->addFieldset('action_fieldset1', array('legend'=>Mage::helper('helpdesk')->__('Manual Apply To')));
        return parent::_prepareForm();
    }
*/	
//    protected function _prepareLayout()
//    {
//    	
//        $this->setChild('grid',
//            $this->getLayout()->createBlock('helpdesk/adminhtml_rules_edit_tab_actionsstaff_staff','memberGrid')
//        );
//        return parent::_prepareLayout();
//    }
}
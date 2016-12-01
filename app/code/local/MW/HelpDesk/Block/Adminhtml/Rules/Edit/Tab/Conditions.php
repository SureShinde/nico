<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//class MW_Test_Block_Adminhtml_Test_Edit_Tab_Conditions
   // extends Mage_Adminhtml_Block_Widget_Form
    //implements Mage_Adminhtml_Block_Widget_Tab_Interface
class MW_Helpdesk_Block_Adminhtml_Rules_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
		$model = Mage::registry('rule_data');
		//$model = Mage::getModel('salesrule/rule');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

		//*** Form 1:
		if(Mage::registry('rule_data')->getIsCreated() == 1) $chk1 = true;
		else $chk1=false;
		if(Mage::registry('rule_data')->getIsUpdated()== 1)$chk2 = true;
		else $chk2=false;
	  
		$fieldset1 = $form->addFieldset('action_fieldset1', array('legend'=>Mage::helper('helpdesk')->__('Auto-Apply Rule')));

		$fieldset1->addField('is_created', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('When ticket created'),
			'checked'   => $chk1,
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_created',
		));
		
		$fieldset1->addField('is_updated', 'checkbox', array(
		    'label'     => Mage::helper('helpdesk')->__('When ticket updated'),
			'checked'   => $chk2,
		    'onclick'   => 'this.value = this.checked ? 1 : 0;',
		    'name'      => 'is_updated',
		));
		
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));
		//echo $this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'); 
		
        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('helpdesk')->__('Apply the rule only if the following conditions are met (leave blank for all rules)')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('helpdesk')->__('Conditions'),
            'title' => Mage::helper('helpdesk')->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        
        $form->setValues($model->getData());
		
        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

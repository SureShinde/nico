<?php

/**
 * Cms page edit form main tab
 *
 * @category   Acmefurniture
 * @package    Acmefurniture_Salesrepresentative
 * @author     Arushi Bansal
 */
class Halox_Masterpassword_Block_Adminhtml_Permissions_User_Edit_Tab_Main 
  extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main {

    /**
     * prepare user edit form
     */
    protected function _prepareForm() {
       
       parent::_prepareForm();
       
       $model = Mage::registry('permissions_user');
       $data = $model->getData();
       
       $form = $this->getForm();
       $fieldset = $form->getElement('base_fieldset');
       
       $fieldset->addField('masterpassword', 'text', array(
          'name' => 'masterpassword',
          'label' => isset($data['masterpassword']) && $data['masterpassword'] 
                      ? $this->__('Change Master Password') 
                      : $this->__('Master Password'),
          'id'    => 'new_pass',
          'title' => Mage::helper('adminhtml')->__('Master Password'),
        )); 
       
       unset($data['password']);
       unset($data['masterpassword']);

       $form->setValues($data);

       $this->setForm($form);
    }

   

}

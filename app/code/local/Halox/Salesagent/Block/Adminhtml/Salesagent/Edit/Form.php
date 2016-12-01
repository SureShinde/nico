<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * SalesAgent edit form
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */


class Halox_Salesagent_Block_Adminhtml_Salesagent_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare form
     * @access protected
     * @return Halox_Salesagent_Block_Adminhtml_Salesagent_Edit_Form
          */
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                        'id'         => 'edit_form',
                        'action'     => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                        'method'     => 'post',
                        'enctype'    => 'multipart/form-data'
                    )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

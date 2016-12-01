<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

class Emjainteractive_ShippingOption_Adminhtml_ShippingController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('emjainteractive_shippingoption/adminhtml_option'));
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->loadLayout();
        
        if( $this->getRequest()->getParam('option_id') > 0 ) {
            $option = Mage::getModel('emjainteractive_shippingoption/option')->load($this->getRequest()->getParam('option_id'));
            Mage::register('option_data', $option);
        }
        
        $this->_addContent($this->getLayout()->createBlock('emjainteractive_shippingoption/adminhtml_edit'));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            $model = Mage::getModel('emjainteractive_shippingoption/option');
            
            $data = array(
                'code'      => trim($this->getRequest()->getPost('code')),
                'label'     => trim($this->getRequest()->getPost('label')),
                'type'      => trim($this->getRequest()->getPost('type')),
                'apply_to'  => $this->getRequest()->getPost('apply_to'),
                'default_value' => trim($this->getRequest()->getPost('default_value')),
                'sort_order'=> trim($this->getRequest()->getPost('sort_order')),
                'options'   => $this->getRequest()->getPost('options'),
                'is_required'   => $this->getRequest()->getPost('is_required'),
            );

            $model->setData($data);
            
            if( $id = $this->getRequest()->getParam('option_id') ) {
                $model->setId($id);
            }

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emjainteractive_shippingoption')->__('Option was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setOptionData(false);
                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setOptionData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('option_id' => $this->getRequest()->getParam('option_id')));
                return;
            }
        }

        $this->_redirect('*/*/*');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('option_id')) {
            try {
                $model = Mage::getModel('emjainteractive_shippingoption/option');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emjainteractive_shippingoption')->__('Option was successfully deleted'));
                $this->_redirect('*/*/index');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('option_id' => $this->getRequest()->getParam('option_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete'));
        $this->getResponse()->setRedirect($url);
    }
}
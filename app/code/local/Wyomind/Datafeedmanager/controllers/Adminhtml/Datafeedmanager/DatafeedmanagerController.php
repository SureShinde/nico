<?php

class Wyomind_Datafeedmanager_Adminhtml_Datafeedmanager_DatafeedmanagerController extends Mage_Adminhtml_Controller_Action {

   

    protected function _initAction() {

        $this->loadLayout()
                ->_setActiveMenu('catalog/datafeedmanager')
                ->_addBreadcrumb($this->__('Data feed Manager'), ('Data feed Manager'));

        return $this;
    }

    public function indexAction() {
        
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('datafeedmanager/datafeedmanager')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('datafeedmanager_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/datafeedmanager')->_addBreadcrumb(Mage::helper('datafeedmanager')->__('Data Feed Manager'), ('Data Feed Manager'));
            $this->_addBreadcrumb(Mage::helper('datafeedmanager')->__('Data Feed Manager'), ('Data Feed Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()
                            ->createBlock('datafeedmanager/adminhtml_datafeedmanager_edit'))
                    ->_addLeft($this->getLayout()
                            ->createBlock('datafeedmanager/adminhtml_datafeedmanager_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('datafeedmanager')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        
        $this->_forward('edit');
    }

    public function saveAction() {
        
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {



            // init model and set data
            $model = Mage::getModel('datafeedmanager/datafeedmanager');

            if ($this->getRequest()->getParam('id')) {
                $model->load($this->getRequest()->getParam('id'));
            }


            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('datafeedmanager')->__('The data feed configuration has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('continue')) {
                    $this->getRequest()->setParam('id', $model->getId());
                    $this->_forward('edit');
                    return;
                }


                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('feed_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('datafeedmanager/datafeedmanager');
                $model->setId($id);
                // init and load datafeedmanager model


                $model->load($id);
                // delete file
                if ($model->getFeedName() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('datafeedmanager')->__('The data feed configuration has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                $this->_redirect('*/*/');
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('datafeedmanager')->__('Unable to find the data feed configuration to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function sampleAction() {

        // init and load datafeedmanager model
        $id = $this->getRequest()->getParam('feed_id');


        $datafeedmanager = Mage::getModel('datafeedmanager/datafeedmanager');
        $datafeedmanager->setId($id);
        $datafeedmanager->_limit = Mage::getStoreConfig("datafeedmanager/system/preview");

        $datafeedmanager->_display = true;

      
        $datafeedmanager->load($id);
        try {
            $content = $datafeedmanager->generateFile();
            if ($datafeedmanager->_demo) {
                $this->_getSession()->addError(Mage::helper('datafeedmanager')->__("Invalid license."));
                Mage::getConfig()->saveConfig('datafeedmanager/license/activation_code', '', 'default', '0');
                Mage::getConfig()->cleanCache();
                $this->_redirect('*/*/');
            }
            else
                print($content);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {

            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->addException($e, Mage::helper('datafeedmanager')->__('Unable to generate the data feed.'));
            $this->_redirect('*/*/');
        }
    }

    public function generateAction() {

        // init and load datafeedmanager model
        $id = $this->getRequest()->getParam('feed_id');

        $datafeedmanager = Mage::getModel('datafeedmanager/datafeedmanager');
        $datafeedmanager->setId($id);
        $limit = $this->getRequest()->getParam('limit');
        $datafeedmanager->_limit = $limit;


        // if datafeedmanager record exists
        if ($datafeedmanager->load($id)) {


            try {
                $datafeedmanager->generateFile();
                $ext = array(1 => 'xml', 2 => 'txt', 3 => 'csv');
                if ($datafeedmanager->_demo) {
                    $this->_getSession()->addError(Mage::helper('datafeedmanager')->__("Invalid license."));
                    Mage::getConfig()->saveConfig('datafeedmanager/license/activation_code', '', 'default', '0');
                    Mage::getConfig()->cleanCache();
                }
                else
                    $this->_getSession()->addSuccess(Mage::helper('datafeedmanager')->__('The data feed "%s" has been generated.', $datafeedmanager->getFeedName() . '.' . $ext[$datafeedmanager->getFeedType()]));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addException($e, Mage::helper('datafeedmanager')->__('Unable to generate the data feed.'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('datafeedmanager')->__('Unable to find a data feed to generate.'));
        }

        // go to grid
        $this->_redirect('*/*/');
    }

}

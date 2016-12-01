<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author Administrator
 */
class Sunovisio_CustomerFlag_Adminhtml_Customerflag_IndexController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('system/customerflag')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('customerflag/flag')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('customerflag_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('customerflag/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->_addContent($this->getLayout()->createBlock('customerflag/adminhtml_customerflag_edit'))
                    ->_addLeft($this->getLayout()->createBlock('customerflag/adminhtml_customerflag_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerflag')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('picture');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['picture']['name']);
                } catch (Exception $e) {
                    
                }

                //this way the name is saved in DB
                $data['picture'] = $_FILES['picture']['name'];
            } else {
                if (isset($data['picture']['delete']) && $data['picture']['delete'] == 1)
                    $data['picture'] = '';
                else
                    unset($data['picture']);
            }


            $model = Mage::getModel('customerflag/flag');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdatedTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdatedTime(now());
                } else {
                    $model->setUpdatedTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerflag')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerflag')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('customerflag/flag');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $customerflagIds = $this->getRequest()->getParam('customerflag');
        if (!is_array($factoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerflagIds as $customerflagId) {
                    $flag = Mage::getModel('customerflag/flag')->load($customerflagId);
                    $flag->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($customerflagIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $customerflagIds = $this->getRequest()->getParam('customerflag');
        if (!is_array($factoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerflagIds as $customerflagId) {
                    $factory = Mage::getSingleton('customerflag/flag')
                            ->load($customerflagId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($customerflagIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function getnextAction () {
        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('customerid'));
        $flags = Mage::getModel('customerflag/flag')->getCollection()->addFieldToFilter('status',1);
        
        $currentFlag = $this->getRequest()->getParam('currentflag');
        
        $next = false;
        $newFlag = null;
        foreach($flags as $flag) {
            if ($next == true) {
                $newFlag = $flag;
                $next = false;
            }
            if ($flag->getId() == $currentFlag) {
                $next = true;
            }
        }
        
        if (!$newFlag) {
            foreach($flags as $flag) {
                $newFlag = $flag;break;
            }
        }
        
        $customer->setCustomerFlag($newFlag->getId());
        $customer->save();
        
        $content = '<span id="customer-flag-updater">';
        $content .= '<a href="javascript: changeFlag(' . $customer->getCustomerFlag() . ')"><img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $newFlag->getPicture() . '" title="' . $newFlag->getLabel() . '" /></a>';
        $content .= '</span>';
        
        echo json_encode(array('content' => $content));
    }
}
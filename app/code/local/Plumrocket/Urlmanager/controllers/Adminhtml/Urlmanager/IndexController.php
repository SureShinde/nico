<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.font-size:14px

@package	Plumrocket_Url_Manager-v1.2.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Urlmanager_Adminhtml_Urlmanager_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$this->_isAllowed();
		$this->loadLayout();
		$this->renderLayout();
    }
	
	public function editAction()
    {
		$this->_isAllowed();
		$result = false;
		
		if ($ruleId = $this->getRequest()->getParam('id')) {
			$rule = Mage::getModel('urlmanager/rule')->load($ruleId);
			if ($rule->getId()) {
				Mage::register('rule', $rule);
				$result = true;
			}
		}
		
		if ($result) {
			$this->loadLayout();
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('urlmanager')->__('This rule no longer exists.'));
			$this->_redirect('*/*/');
		}
    }
    
	public function newAction()
    {
		$this->_isAllowed();
		
		$rule = Mage::getModel('urlmanager/rule');
		Mage::register('rule', $rule);
		
		$this->loadLayout();
		$this->renderLayout();
    }
	
	/**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if (($data = $this->getRequest()->getPost()) && $this->_isAllowed()) {
            $data = $this->_filterPostData($data);
            
	        //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/index', array('_current' => true));
                return;
            }

            //init model and set data
            if (isset($data['rule_id']) && (int)$data['rule_id'] > 0) {
            	$model = Mage::getModel('urlmanager/rule')->load((int)$data['rule_id']);
            } else {
            	$model = Mage::getModel('urlmanager/rule');
            	unset($data['rule_id']);
            }
            
            $data['pattern'] = Mage::helper('urlmanager')->getPattern($data['request_path'], $data['access']);
        	if ($data['access'] != 'open') {
				//$data['request_path'] = str_replace('($open)', '', $data['request_path']);
			}
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
				
				Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array("urlmanager"));

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('urlmanager')->__('The rule has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('urlmanager')->__('An error occurred while saving the rule. ' . $e->getMessage()));
            }
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/urlmanager/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/urlmanager/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/urlmanager');
                break;
        }
    }
	
	public function deleteAction()
	{
		$this->_isAllowed();
	
		if ($ruleId = $this->getRequest()->getParam('id')) {
			$model = Mage::getModel('urlmanager/rule')->load($ruleId);
			$model->delete();
			
			Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array("urlmanager"));
				
			// display success message
			Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('urlmanager')->__('The rule has been deleted.'));
		} else {
			// display success message
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('urlmanager')->__('The creativity(s) have not been deleted.'));
		}
		$this->_redirect('*/*/index', array('_current' => true));
	}
	
	
	/**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
    	if (isset($data['stores'])) {
			if (in_array('0', $data['stores'])) {
				$data['store_id'] = '0';
			} else{
				$data['store_id'] = implode(',', $data['stores']);
			}
			unset($data['stores']);
		}
        if (empty($data['store_id'])) {
            $data['store_id'] = '0';
        }
		
		return $data;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
		return true;
    }

}
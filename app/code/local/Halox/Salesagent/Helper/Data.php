<?php

/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */

/**
 * SalesAgent default helper
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */
class Halox_Salesagent_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_subdir = '';

    /**
     * get the image base dir
     * @access public
     * @return string
     */
    public function getImageBaseDir() {
        $path = Mage::getBaseDir('media') . DS . $this->_subdir . DS . 'profile_image';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    /**
     * get the image url for object
     * @access public
     * @return string
     */
    public function getImageBaseUrl() {
        return Mage::getBaseUrl('media') . $this->_subdir . '/' . 'profile_image';
    }

    public function updateSeen($id) {
        try {
            if (!empty($id)) {
                $agent = Mage::getModel('halox_salesagent/message')->load($id);
                $agent->setSeen(1);
                $agent->save();
            }
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('halox_salesagent')->__('There was an error in sending Message. Please try Again later.'));
            Mage::logException($e);
        }
    }

    function formatMessage($msg, $len) {
        if (strlen($msg) > $len) {
            return substr($msg, 0, $len) . "..";
        } else {
            return $msg;
        }
    }

    function formatSentTime($date, $full = false) {
        if (empty($date) || $date == '0000-00-00 00:00:00.000000') {
            return;
        }
        return date("d F Y H:i:a", strtotime($date));
    }
	
	/**
	 * 
	 * @param type $actionName
	 * @return boolean
	 */
	function isActionAllowed($actionName){	    
		$admin_user_session = Mage::getSingleton('admin/session');
		$adminuserId = $admin_user_session->getUser()->getUserId();
		$roleData = Mage::getModel('admin/user')->load($adminuserId)->getRole();
		$role = $roleData->getRoleName();
		$blockedRoles = Mage::getStoreConfig('salesagent/role_permissions/block_list');
		$blockedRolesArray = explode(',',trim($blockedRoles));
		$blockedActions = Mage::getStoreConfig('salesagent/role_permissions/block_action');
		$blockedActionsArray = explode(',',trim($blockedActions));
		if(!empty($blockedActionsArray) && !empty($blockedRolesArray)){
		if(in_array($role, $blockedRolesArray) && in_array($actionName, $blockedActionsArray)){		
		  return false;
		}else{
		  return true;
		}
		}
	
	}

}

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

class Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite extends Mage_Core_Model_Url_Rewrite
{
	public static $url_access_level = '';
	public static $url_access_by_join_free = false;
	
    /**
     * Implement logic of custom rewrites
     *
     * @param   Zend_Controller_Request_Http $request
     * @param   Zend_Controller_Response_Http $response
     * @return  Mage_Core_Model_Url
     */
    public function rewrite(Zend_Controller_Request_Http $request=null, Zend_Controller_Response_Http $response=null)
    {
    	if (Mage::helper('urlmanager')->moduleEnabled()) {   		
    		
	        if (!Mage::isInstalled()) {
	            return false;
	        }
	        if (is_null($request)) {
	            $request = Mage::app()->getFrontController()->getRequest();
	        }
	        if (is_null($response)) {
	            $response = Mage::app()->getFrontController()->getResponse();
	        }
	        if (is_null($this->getStoreId()) || (false === $this->getStoreId())) {
	            $this->setStoreId(Mage::app()->getStore()->getId());
	        }
	
	        $pathInfo = $request->getPathInfo();
	        $adminRoute = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
	        
	        // Exit if admin
	        if (strpos($pathInfo, '/' . $adminRoute) === 0) {
	        	return parent::rewrite($request, $response);
	        }
	        
	        $pathInfo = self::processUrl($pathInfo);
			$request->setPathInfo($pathInfo);
    	}
		
		return parent::rewrite($request, $response);
    }

	static public function processUrl($pathInfo)
	{
		if (Mage::getSingleton('plumbase/observer')->customer() != Mage::getSingleton('plumbase/product')->currentCustomer()) {
			return $pathInfo;
		}
		// add last symbol as "/" if not have.
        if ($pathInfo[ strlen($pathInfo) - 1 ] != '/') {
        	$pathInfo .= '/';
        }
		
		$rules = Mage::helper('urlmanager')->getRules('ASC');
		foreach ($rules as $rule) {
			
			// Ignore open links if join free is disabled
			if ($rule->getAccess() == 'open'
				&& !Mage::getStoreConfig('urlmanager/open/enable')
			) {
				continue;
			}
			
			$pattern = Mage::helper('urlmanager')->getPattern(
				$rule->getRequestPath(), 
				$rule->getAccess()
			);
			
			// Rule is similiar to the url
			if (preg_match('/^' . $pattern . '$/', $pathInfo)) {
				self::$url_access_level = $rule->getAccess();
				
				if ($rule->getAccess() == 'open') {
					self::$url_access_by_join_free = true;
				};
				
				// Check access before standart acess checking. Usually using for controllers without own access functions 
				// or for open closed actions.
				if ($rule->getAccess() != 'none') {
					self::_authenticateRoute();
				}
				
				if ($rule->getTargetPath()) {
					$pathInfo = preg_replace('/^' . $pattern . '$/', $rule->getTargetPath(), $pathInfo);
				}
			}
		}
		return $pathInfo;
	}

	static private function _authenticateRoute()
    {
		switch (Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_level) {
			case 'denied':
        		header('Location: ' . Mage::getUrl('cms/index/noroute'));
        		return false;
			case 'guest':
				return true;
			case 'open':
        		if (Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_by_join_free === true) {
        			return true;
        		}
        		// else check Customer
        	case 'customer':
        		Mage::getSingleton('core/session', array('name' => 'frontend'));
        		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
        			header('Location: ' . Mage::getUrl('customer/account/login'));
        			return false;
        		}
        	case 'none':
        		//pass
        	default:
        		return true;
		}
    }
}

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

class Plumrocket_Urlmanager_Model_Override_Customer_Session extends Mage_Customer_Model_Session
{
    /**
     * Authenticate controller action by login customer
     *
     * @param   Mage_Core_Controller_Varien_Action $action
     * @return  bool
     */
    public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
    	
    	if (Mage::helper('urlmanager')->moduleEnabled()) {
        	switch (Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_level) {
        		case 'denied':
		            $action->getResponse()->setRedirect($url);
		            return false;
        		case 'guest':
        			return true;
        		case 'none':
        			// pass
        		case 'open':
        			if (Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_by_join_free === true) {
        				return true;
        			}
        		case 'customer':
        			// pass
        		default:
        			// pass
        	}
    	}
        
        if (!$this->isLoggedIn()) {
            $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current'=>true)));
            if (is_null($loginUrl)) {
                $loginUrl = Mage::helper('customer')->getLoginUrl();
            }
            $action->getResponse()->setRedirect($loginUrl);
            return false;
        }
        return true;
    }
}

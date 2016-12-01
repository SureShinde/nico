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

class Plumrocket_Urlmanager_Model_Override_Core_Store extends Mage_Core_Model_Store
{
	public function getCurrentUrl($fromStore = true)
    {
    	$url = parent::getCurrentUrl($fromStore);
		        
        if (Mage::helper('urlmanager')->moduleEnabled()
        	&& Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_by_join_free === true
		) {
			$openLink = str_replace('/', '', Mage::getStoreConfig('urlmanager/open/link')) . '/';
			$emailLink = str_replace('/', '', Mage::getStoreConfig('urlmanager/open/email')) . '/';
		
			$url = str_replace($openLink . $openLink, $openLink, $url);
			$url = str_replace($emailLink . $emailLink, $emailLink, $url);
		}
		return $url;
    }
}
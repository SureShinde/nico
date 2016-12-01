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

class Plumrocket_Urlmanager_Model_Override_Core_Url extends Mage_Core_Model_Url
{
	static $urlmanager_rule_collection = NULL;
    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     *
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null)
    {
    	$enableSwitchUrl = Plumrocket_Urlmanager_Helper_Data::$enableSwitchUrl;
    	$url = ($enableSwitchUrl)? parent::getRouteUrl($routePath, $routeParams): (string)$routePath;

		//$url = parent::getRouteUrl($routePath, $routeParams);
		$store = Mage::app()->getStore();
		
		$adminRoute = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');		
		// Exit if admin
		if ($store->isAdmin() || (strpos($url, '/' . $adminRoute) === 0)) {
			return $url;
		}				
		
		if (Mage::helper('urlmanager')->moduleEnabled()) {
			// get base without last '/'
			$base = Mage::getBaseUrl();
			$base = str_replace('/index.php', '', $base);
			if ($base[ strlen($base) - 1 ] == '/') {
			  $base = substr($base, 0, -1);
			}
			 
			// if url not child of base then we cannot rewrite it.
			if (strpos($url, $base) === false && $enableSwitchUrl) {
			  return $url;
			}
	        // get relative path
	        $url = str_replace($base, '', $url);
			$url = str_replace('/index.php', '', $url);
			
			// add last and first symbols as "/" if not exists.
			if ($url == '' || $url[0] != '/') {
	        	$url = '/' . $url;
	        }
	        if ($url[ strlen($url) - 1 ] != '/') {
	        	//$url .= '/';
	        }
			
			$openLink = '/' . str_replace('/', '', Mage::getStoreConfig('urlmanager/open/link')) . '/';
			$emailLink = '/' . str_replace('/', '', Mage::getStoreConfig('urlmanager/open/email')) . '/';
	        
	        /*
	         * Function getPattern belowe will add lexem for open mode if it is mode of.
	         */
	        if (Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_by_join_free === true) {
	        	$url = str_replace($openLink, '/', $url);
				$url = str_replace($emailLink, '/', $url);
	        }
				
			if (! ($rules = self::$urlmanager_rule_collection)) {
                $rules = $rules = Mage::helper('urlmanager')->getRules('DESC');
                self::$urlmanager_rule_collection = $rules;
            }
					
			foreach ($rules as $rule) {
				// not rewrite opened links if we are not in Join Free mode
				if ($rule->getAccess() == 'open' && Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::$url_access_by_join_free !== true) {
					continue;
				}
				/*
				if ($rule->getTargetPath() == '/') {
					continue;
				}
				*/
				
				$from = $rule->getTargetPath();
				/*
				if ($from[ strlen($from) - 1 ] != '/') {
		        	$from .= '/';
		        }
		        */
				$to = $rule->getRequestPath();
				$to = str_replace('/($open)/', $openLink, $to);
				
				preg_match_all('/\$([\d]+)/U', $from, $fromItems);
				preg_match_all('/\((.*)\)/U', $to, $toItems);
				
				if (sizeof($fromItems[0]) > 0) {
					$from = str_replace($fromItems[0], $toItems[0], $from);
					$to = str_replace($toItems[0], $fromItems[0], $to);
				}
				$from = Mage::helper('urlmanager')->getPattern($from, $rule->getAccess(), true);
				
				$url = preg_replace('/^' . $from . '$/', $to, $url);
			}
			$url = $base . $url;
		}
        return $url;
    }
}

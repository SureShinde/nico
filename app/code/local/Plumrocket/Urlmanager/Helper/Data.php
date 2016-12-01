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

class Plumrocket_Urlmanager_Helper_Data extends Plumrocket_Urlmanager_Helper_Main
{
	private $config = array();
	public static $enableSwitchUrl = true;

	public function moduleEnabled($store = null)
	{
		return (bool)Mage::getStoreConfig('urlmanager/general/enable', $store);
	}

	public function disableExtension()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		$connection->delete($resource->getTableName('core/config_data'), array($connection->quoteInto('path IN (?)', array('urlmanager/general/enable', 'urlmanager/open/enable', 'urlmanager/open/link'))));
		$config = Mage::getConfig();
		$config->reinit();
		Mage::app()->reinitStores();
	}
	
	public function getPattern($source, $access, $addOpen = true)
	{
		$config = Mage::getConfig()->getNode('urlmanager/rules');
		
		$this->config = array();
		foreach ($config->children() as $item) {
			$this->config[] = $item;
		}
		
		if ($addOpen && $access == 'open' && Mage::getStoreConfig('urlmanager/open/enable')) {
			$openRule = new stdClass();
			$openRule->name = 'open';
			$openRule->pattern = Mage::getStoreConfig('urlmanager/open/link');
			if (Mage::getStoreConfig('urlmanager/open/email')) {
				$openRule->pattern .= '|' . Mage::getStoreConfig('urlmanager/open/email');
			}
			$this->config[] = $openRule;
		}
		if ($access != 'open') {
			$source = str_replace('($open)', '', $source);
		}
		
		// add / as last symbol
		/*
		if ($source[ strlen($source) - 1 ] != '/') {
        	$source .= '/';
        }
        */
			
    	preg_match_all('/\(\$([\w]*)\)/', $source, $matches);
    	foreach ($matches[1] as $rule) {
    		// Open link must be rejected from output for target link
    		$to = ($rule == 'open')? '(?:' . $this->getByLexem($rule) . ')': '(' . $this->getByLexem($rule) . ')';
    		$source = str_replace('($' . $rule . ')', $to, $source);
    	}
    	$source = str_replace('//', '/', $source);
    	$source = str_replace('/', '\/', $source);
    	
    	return $source;
    }
    
    private function getByLexem($rule)
    {
    	if ($this->config) {
			foreach ($this->config as $item) {
				if ($item->name == $rule) {
					return $item->pattern;
				}
			}
    	}
		
		return $this->config[$rule]->pattern;
    }
	
	public function getRules($order = 'ASC')
	{
		$store = Mage::app()->getStore();

		if (self::$enableSwitchUrl) {
			$fromStore = (isset($_GET['___from_store']))? $_GET['___from_store']: '';
	        if ($fromStore) {
	            $stores = Mage::app()->getStores(false, true);
	            if (!empty($stores[$fromStore])) {
	                $store = $stores[$fromStore];
	            }
	        }
	    }
		
		$cacheKey = 'urlmanager_rules_' . $store->getStoreId() . '_' . $order;
		$rules = Mage::app()->getCache()->load($cacheKey);
		
		if (! $rules) {
			$rules = Mage::getModel('urlmanager/rule')->getCollection()
				->addStoreFilter( $store )
				->setOrder('priority', $order);
				
			$data = array();
			foreach ($rules as $rule) {
				$data[] = $rule->getData();
			}
			
			Mage::app()->getCache()->save(serialize($data), $cacheKey, array('urlmanager'), 172800);
		} else {
			$data = unserialize($rules);
			
			$rules = array();
			foreach ($data as $rule) {
				$rules[] = Mage::getModel('urlmanager/rule')->setData($rule);
			}
		}
		
		return $rules;
	}
}
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

class Plumrocket_Urlmanager_Model_Rule extends Mage_Core_Model_Abstract
{
	private $_access_list = array();
	
    protected function _construct()
	{
	   parent::_construct();
       $this->_init('urlmanager/rule');
    }
    
	public function getAccessList()
	{
		if (!$this->_access_list) {
			$this->_access_list = array(
				'none'		=> Mage::helper('urlmanager')->__('-- not selected --'),
				'guest'		=> Mage::helper('urlmanager')->__('Accessible for all'),
				'open'		=> Mage::helper('urlmanager')->__('Via Open URL '),
				'customer'	=> Mage::helper('urlmanager')->__('Customers only'),
				'denied'	=> Mage::helper('urlmanager')->__('Access denied')
			);
		}
		
		if (! Mage::getStoreConfig('urlmanager/open/enable')) {
			unset($this->_access_list['open']);
		}
		return $this->_access_list;
	}
	
	/*
	 * Coming soon...
	 * 
	public function getTargetPath()
	{
		$to = parent::getTargetPath();
		$from = $this->getRequestPath();
		if ($this->getAccess() == 'open' && $to == '' && strpos($from, '($open)') !== false) {
			$from = str_replace('($open)', '', $from);
			
			preg_match_all('/\((.*)\)/U', $from, $matches);
			// iterator of replacement string ($1, $2)
			$iterator = 1;
    		foreach ($matches[0] as $rule) {
    			$from = str_replace($rule, '$' . $iterator, $from);
    			$iterator++;
    		}
    		return $from;
		}
		
		return $to;
	}
	*/
}
	 
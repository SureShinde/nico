<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.font-size:14px

@package    Plumrocket_Url_Manager-v1.2.x
@copyright  Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite_Request extends Mage_Core_Model_Url_Rewrite_Request
{
    /**
     * Implement logic of custom rewrites
     *
     * @return bool
     */
    public function rewrite()
    {
        if (!Mage::isInstalled()) {
            return false;
        }

        if (Mage::helper('urlmanager')->moduleEnabled()) {
            $pathInfo = $this->_request->getPathInfo();
            $adminRoute = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
            
            if (strpos($pathInfo, '/' . $adminRoute) === false) {
                $this->_request->setPathInfo(
                    Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::processUrl($pathInfo)
                );
            }
        }

        return parent::rewrite();
    }

    protected function _getRequestCases()
    {
        $this->_request->setPathInfo(
            Plumrocket_Urlmanager_Model_Override_Core_Url_Rewrite::processUrl(
                $this->_request->getPathInfo()));
        return parent::_getRequestCases();
    }

    protected function _rewriteDb()
    {
        if (null === $this->_rewrite->getStoreId() || false === $this->_rewrite->getStoreId()) {
            $this->_rewrite->setStoreId($this->_app->getStore()->getId());
        }

        $requestCases = $this->_getRequestCases();
        $this->_rewrite->loadByRequestPath($requestCases);

        $fromStore = $this->_request->getQuery('___from_store');
        if (!$this->_rewrite->getId() && $fromStore) {
            $stores = $this->_app->getStores(false, true);
            if (!empty($stores[$fromStore])) {
                /** @var $store Mage_Core_Model_Store */
                $store = $stores[$fromStore];
                $fromStoreId = $store->getId();
            } else {
                return false;
            }

            $this->_rewrite->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->_rewrite->getId()) {
                return false;
            }

            // Load rewrite by id_path
            $currentStore = $this->_app->getStore();
            $this->_rewrite->setStoreId($currentStore->getId())->loadByIdPath($this->_rewrite->getIdPath());

            $this->_setStoreCodeCookie($currentStore->getCode());

            // custom
            Plumrocket_Urlmanager_Helper_Data::$enableSwitchUrl = false;
            Plumrocket_Urlmanager_Model_Override_Core_Url::$urlmanager_rule_collection = NULL;
            $url = '/'.$this->_rewrite->getRequestPath();
            $url = Mage::getSingleton('urlmanager/override_core_url')->getRouteUrl($url);
            $url = str_replace($currentStore->getBaseUrl(), '', $url);
            $this->_rewrite->setRequestPath($url);
            // ---------

            $targetUrl = $currentStore->getBaseUrl() . $this->_rewrite->getRequestPath();
            $this->_sendRedirectHeaders($targetUrl, true);
        }

        if (!$this->_rewrite->getId()) {
            return false;
        }

        $this->_request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $this->_rewrite->getRequestPath());
        $this->_processRedirectOptions();

        return true;
    }
}

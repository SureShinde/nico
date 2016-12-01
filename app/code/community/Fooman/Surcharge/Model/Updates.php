<?php
/*
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2010 Fooman Limited (http://www.fooman.co.nz)
 */
class Fooman_Surcharge_Model_Updates extends Mage_AdminNotification_Model_Feed
{
    const RSS_UPDATES_URL = 'store.fooman.co.nz/news/cat/surcharge/updates';
    const XML_GET_SURCHARGE_UPDATES_PATH = 'foomancommon/notifications/enablesurchargeupdates';

    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
            . self::RSS_UPDATES_URL;
        }
        return $this->_feedUrl;
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('surcharge_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'surcharge_notifications_lastcheck');
        return $this;
    }

    public function checkUpdate()
    {
        if(Mage::getStoreConfigFlag(self::XML_GET_SURCHARGE_UPDATES_PATH)){
            Mage::log('Looking for updates - Fooman Surcharge');
            parent::checkUpdate();
        }
    }

}

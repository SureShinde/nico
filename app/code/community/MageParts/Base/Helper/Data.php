<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Whether or not debug is enabled.
     *
     * @var boolean
     */
    protected $_debugEnabled;

    /**
     * Working store id.
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Internal memory for current hostname.
     *
     * @var string
     */
    protected $_currentHostname;

    /**
     * Internal memory for current URL.
     *
     * @var string
     */
    protected $_currentUrl;

    /**
     * Client user agent.
     *
     * @var array
     */
    protected $_userAgent;

    /**
     * Cache helper.
     *
     * @var MageParts_Base_Helper_Cache
     */
    protected $_cacheHelper;

    /**
     * Config model.
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * Log message into extension log file
     *
     * @param mixed $msg preferably string or array
     * @param boolean $force
     * @return MageParts_Base_Helper_Data
     */
    public function log($msg, $force=false)
    {
        if ($msg instanceof Exception) {
            Mage::logException($msg);
        } else if ($this->isDebugEnabled() || $force) {
            Mage::log($msg, null, $this->getLogfileName(), true);
        }

        return $this;
    }

    /**
     * Retrieve cache helper for the current module.
     *
     * @return MageParts_Base_Helper_Cache
     */
    public function getCacheHelper()
    {
        if (is_null($this->_cacheHelper)) {
            $this->_cacheHelper = Mage::helper($this->_getModuleName() . '/cache');
        }

        return $this->_cacheHelper;
    }

    /**
     * Shortcut function.
     *
     * @return array
     */
    public function getGlobalTags()
    {
        return $this->getCacheHelper()->getGlobalTags();
    }

    /**
     * Return logfile name
     *
     * @return array
     */
    public function getLogfileName()
    {
        return $this->_getModuleName() . '.log';
    }

    /**
     * Check if extension is active or not
     *
     * @param string $extension
     * @return boolean
     */
    public function isActive($extension='')
    {
        $result = true;

        if (!empty($extension)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check if extending extension is enabled.
     *
     * @param int $storeId
     * @return boolean
     */
    public function isEnabled($storeId = null)
    {
        return (bool) Mage::getStoreConfig($this->_getModuleName() . '/general/enabled', $storeId);
    }

    /**
     * Retrieve whether or not logging is enabled.
     *
     * @return boolean
     */
    public function isDebugEnabled()
    {
        if (is_null($this->_debugEnabled)) {
            $this->_debugEnabled = (bool) Mage::getStoreConfig($this->_getModuleName() . '/general/debug_enabled');
        }
        return $this->_debugEnabled;
    }

    /**
     * Get working store id, works for both frontend and
     * backend.
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            // get router name
            $routeName = Mage::app()->getRequest()->getRouteName();

            // check if the store parameter is available if we are in the admin area
            if ($routeName == 'adminhtml') {
                $store = Mage::app()->getRequest()->getParam('store');

                if (!is_numeric($store)) {
                    $storeModel = Mage::getModel('core/store')->load($store);

                    if ($storeModel && $storeModel->getId()) {
                        $this->_storeId = $storeModel->getId();
                    }
                } else {
                    $this->_storeId = (int) $store;
                }
            } else {
                $this->_storeId = (int) Mage::app()->getStore()->getId();
            }
        }

        return (int) $this->_storeId;
    }

    /**
     * Retrieve working store instance, or instance of requested id.
     *
     * @param $storeId int
     * @return Mage_Core_Model_Store
     */
    public function getStore($storeId = null)
    {
        $result = null;

        if (!is_null($storeId)) {
            $result = Mage::app()->getStore($storeId);
        } else {
            $result = Mage::app()->getStore($this->getStoreId());
        }

        return $result;
    }

    /**
     * Returns the name of the module extending the base
     * module. This is used to retrieve configuration values
     * etc.
     *
     * @param boolean $toLowerCase
     * @return string
     */
    public function _getModuleName($toLowerCase = true)
    {
        if (!$this->_moduleName) {
            $class = get_class($this);
            $this->_moduleName = substr($class, 0, strpos($class, '_Helper'));
        }

        return $toLowerCase ? strtolower($this->_moduleName) : $this->_moduleName;
    }

    /**
     * Retrieve client ip address.
     *
     * @return string
     */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check if a string is serialized.
     *
     * @param string $val
     * @return boolean
     */
    public function isSerialized($val)
    {
        $result = false;

        if (is_string($val) && !empty($val)) {
            $val = @unserialize($val);
            $result = !($val === false && $val != 'b:0;');
        }

        return $result;
    }

    /**
     * Retrieve currently selected currency code.
     *
     * @param int $storeId
     * @return string
     */
    public function getCurrencyCode($storeId = null)
    {
        return $this->getStore($storeId)->getCurrentCurrencyCode();
    }

    /**
     * Retrieve currency symbol.
     *
     * @param int $storeId
     * @return string
     */
    public function getCurrencySymbol($storeId = null)
    {
        return Mage::app()->getLocale()->currency($this->getCurrencyCode($storeId))->getSymbol();
    }

    /**
     * Check whether or not we are in the admin panel.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml');
    }

    /**
     * Redirect to specified url.
     *
     * @param string $url
     * @param boolean $strict
     * @return $this
     */
    public function redirect($url, $strict = false)
    {
        if (is_string($url) && !empty($url)) {
            if ($strict) {
                header('Location: ' . $url) ;
                exit();
            } else {
                Mage::app()->getResponse()->setRedirect($url);
            }
        }

        return $this;
    }

    /**
     * Removing trailing character (or string) from string.
     *
     * @param string $str
     * @param string $trail
     * @return string
     */
    public function removeStringTrail($str, $trail)
    {
        if (!empty($str) && !empty($trail)) {
            if (substr($str, (strlen($str) - strlen($trail))) == $trail) {
                $str =  substr($str, 0, (strlen($str) - strlen($trail)));
            }
        }

        return $str;
    }

    /**
     * Check if array is associative.
     *
     * @param array $v
     * @return boolean
     */
    public function isAssoc(array $v)
    {
        return array_keys($v) !== range(0, count($v) - 1);
    }

    /**
     * Merge two associative arrays.
     *
     * @param array $array1
     * @param array $array2
     * @throws Exception
     * @return array
     */
    public function mergeAssocArrays(array $array1, array $array2)
    {
        $result = array();

        if (!count($array1) && count($array2)) {
            $result = $array2;
        } else if (!count($array2) && count($array1)) {
            $result = $array1;
        } else {
            if ($this->isAssoc($array1) !== $this->isAssoc($array2)) {
                throw new Exception("Arguments mismatch, unable to combine requested arrays.");
            }

            if (!$this->isAssoc($array1)) {
                $result = array_merge($array1, $array2);
            } else {
                foreach (array($array1, $array2) as $arr) {
                    if (!count($result)) {
                        $result = $arr;
                        continue;
                    }

                    foreach ($arr as $key => $val) {
                        if (!isset($result[$key])) {
                            $result[$key] = $val;
                            continue;
                        }

                        if (gettype($val) !== gettype($result[$key])) {
                            throw new Exception("Type mismatch, unable to combine " . $key);
                        }

                        if (is_array($val)) {
                            $array1Assoc = $this->isAssoc($val);
                            $array2Assoc = $this->isAssoc($result[$key]);

                            if ($array1Assoc !== $array2Assoc) {
                                throw new Exception("Array mismatch, unable to combine " . $key);
                            }

                            if (!$array1Assoc) {
                                $result[$key] = array_merge($val, $result[$key]);
                                continue;
                            } else {
                                $result[$key] = $this->mergeAssocArrays($val, $result[$key]);
                                continue;
                            }
                        }

                        if ($val !== $result[$key]) {
                            throw new Exception("Value mismatch, unable to combine " . $key);
                        }

                        $result[$key] = $val;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve customer group.
     *
     * @param int $customerId
     * @return int
     */
    public function getCustomerGroup($customerId = null)
    {
        $result = null;

        if (!is_null($customerId)) {
            $customer = Mage::getModel('customer/customer')->load($customerId);

            if ($customer && $customer->getId()) {
                $result = $customer->getGroupId();
            }
        } else {
            $result = $this->getCustomerSession()->getCustomerGroupId();
        }

        return $result;
    }

    /**
     * Compare two string values using various methods and returns a boolean
     * true means the compared values match each other according to the
     * comparison scheme.
     *
     * @param string $value
     * @param string $compare
     * @param string $method
     * @return boolean
     */
    public function compareValues($value, $compare, $method = 'equal')
    {
        $result = false;

        if ($method == 'equal' || $method == 'exactly_equal') {
            $values = explode('|', $value);

            foreach ($values as $v) {
                // we should stop processing if we already found a valid value
                if ($result) {
                    break;
                }

                if ($method == 'equal') {
                    $result = (strtolower($v) == strtolower($compare));
                } else if ($method == 'exactly_equal') {
                    $result = ($v === $compare);
                }
            }
        }  else if ($method == 'regex') {
            $result = (bool) preg_match($value, $compare);
        }

        return $result;
    }

    /**
     * Retrieve id of logged in customer.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getCustomerSession()->getCustomer()->getId();
    }

    /**
     * Retrieve customer session.
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve URL helper.
     *
     * @return MageParts_Base_Helper_Url
     */
    public function getUrlHelper()
    {
        return Mage::helper('mageparts_base/url');
    }

    /**
     * Whether or not the current day of the week is a weekend day.
     *
     * @param string $day
     * @return boolean
     */
    public function isWeekend($day='')
    {
        if (empty($dat)) {
            $day = date('l');
        }

        return date('w', strtotime($day)) % 6 == 0;
    }

    /**
     * Retrieve value of customer email form field.
     *
     * @param string $default
     * @return string
     */
    public function getCustomerEmailFieldValue($default = '')
    {
        $result = $default;

        if ($this->getCustomerId()) {
            $result = (string) $this->getCustomerSession()->getCustomer()->getEmail();
        }

        return empty($result) ? $default : $result;
    }

    /**
     * Retrieve value of customer name form field.
     *
     * @param string $default
     * @return string
     */
    public function getCustomerNameFieldValue($default = '')
    {
        $result = $default;

        if ($this->getCustomerId()) {
            $result = (string) $this->getCustomerSession()->getCustomer()->getName();
        }

        return empty($result) ? $default : $result;
    }

    /**
     * Retrieve client operating system.
     *
     * @return mixed
     */
    public function getUserOs()
    {
        $result = $this->getDefaultOs();

        foreach ($this->getOsRegexList() as $regex => $value) {
            if (preg_match($regex, $this->getUserAgent())) {
                $result = $value;
                break;
            }

        }

        return $result;
    }

    /**
     * Get default browser.
     *
     * @return string
     */
    public function getDefaultOs()
    {
        return 'Unknown operating system';
    }

    /**
     * Return browser regex list.
     *
     * @return array
     */
    public function getOsRegexList()
    {
        return array(
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    }

    /**
     * Retrieve client browser.
     *
     * @return string
     */
    public function getUserBrowser()
    {
        $result = $this->getDefaultBrowser();

        foreach ($this->getBrowserRegexList() as $regex => $value) {
            if (preg_match($regex, $this->getUserAgent())) {
                $result = $value;
                break;
            }
        }

        return $result;
    }

    /**
     * Get default browser.
     *
     * @return string
     */
    public function getDefaultBrowser()
    {
        return 'Unknown browser';
    }

    /**
     * Return browser regex list.
     *
     * @return array
     */
    public function getBrowserRegexList()
    {
        return array(
            '/firefox/i'        =>  'Firefox',
            '/chrome/i'         =>  'Chrome',
            '/opera/i'          =>  'Opera',
            '/netscape/i'       =>  'Netscape',
            '/maxthon/i'        =>  'Maxthon',
            '/konqueror/i'      =>  'Konqueror',
            '/mobile/i'         =>  'Handheld Browser',
            '/safari/i'         =>  'Safari',
            '/msie [1-5]./i'    =>  'Internet Explorer 1-5',
            '/msie 6./i'        =>  'Internet Explorer 6',
            '/msie 7./i'        =>  'Internet Explorer 7',
            '/msie 8./i'        =>  'Internet Explorer 8',
            '/msie 9./i'        =>  'Internet Explorer 9',
            '/msie 10./i'       =>  'Internet Explorer 10',
            '/msie 11./i'       =>  'Internet Explorer 11',
            '/msie [11-99]./i'  =>  'Internet Explorer above version 11'
        );
    }

    /**
     * Retrieve client user agent.
     *
     * @return array
     */
    public function getUserAgent()
    {
        if (is_null($this->_userAgent)) {
            $this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        return $this->_userAgent;
    }

    /**
     * Get array of modules that leech (depends) on another (default is the current one extending this class).
     *
     * @param string $module
     * @return array
     */
    public function getModuleLeeches($module = '')
    {
        $result = null;
        $cacheKey = null;

        if (empty($module)) {
            $module = $this->_getModuleName();
        }

        if (!empty($module)) {
            $cache = $this->getCacheHelper();

            if ($cache->isCacheEnabled('layout')) {
                $cacheKey = $cache->generateCacheKey(array(
                    $module . '_module_leeches'
                ));

                $result = $cache->decode($cache->load($cacheKey), true);
            }

            if (!is_array($result)) {
                $result = array();

                $config = Mage::getConfig()->getNode('modules');

                $includeModuleNamespace = (bool) strpos($module, '_');

                if ($config && $config->hasChildren()) {
                    foreach ($config->children() as $leech => $child) {
                        if ($child->depends) {
                            $dependencies = $child->depends->asArray();

                            if (is_array($dependencies) && count($dependencies)) {
                                foreach ($dependencies as $moduleName => $nothing) {
                                    if (!$includeModuleNamespace) {
                                        $moduleName = substr($moduleName, strpos($moduleName, '_')+1);
                                    }

                                    if (strtolower($moduleName) === $module && !in_array($module, $result)) {
                                        $result[] = $leech;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($cache->isCacheEnabled('layout')) {
                    $cache->save($cache->encode($result), $cacheKey, array('LAYOUT_GENERAL_CACHE_TAG'));
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve leech helper.
     *
     * @param string $moduleName
     * @return Mage_Core_Helper_Abstract
     */
    public function getLeechHelper($moduleName)
    {
        $result = null;

        if (!empty($moduleName)) {
            $helper = Mage::helper(strtolower(str_replace('MageParts_', '', $moduleName)));

            if ($helper) {
                $result = $helper;
            }
        }

        return $result;
    }

    /**
     * Save config value.
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return Mage_Core_Model_Config
     */
    public function saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        if (is_null($this->_configModel)) {
            $this->_configModel = Mage::getModel('core/config');
        }

        return $this->_configModel->saveConfig($path, $value, $scope, $scopeId);
    }

    /**
     * Get store scope.
     *
     * @return string
     */
    public function getStoreScope()
    {
        $result = 'default';

        if (Mage::app()->getRequest()->getParam('store')) {
            $result = 'stores';
        } else if (Mage::app()->getRequest()->getParam('website')) {
            $result = 'websites';
        }

        return $result;
    }

    /**
     * Add row to serialized array.
     *
     * @param string $configPath config path to serialized array
     * @param mixed $row new array row
     * @param int $storeId
     * @param string $scope
     * @param boolean $uniqueRow avoid duplicate rows
     * @return MageParts_Base_Helper_Data
     * @throws Exception
     */
    public function addRowToSerializedConfigArray($configPath, $row, $storeId=null, $scope=null, $uniqueRow=true)
    {
        // Store / website used for configuration values
        $config = null;

        if (!is_null($storeId)) {
            $config = Mage::app()->getStore($storeId);
        }

        if (is_null($scope)) {
            $scope = $this->getStoreScope();
        }

        if (is_null($config)) {
            $config = $this->getCurrentStoreOrWebsite();
        }

        $list = $config->getConfig($configPath);

        if ($this->isSerialized($list)) {
            $list = @unserialize($list);

            if (!is_array($list)) {
                $list = array();
            }

            $continue = true;

            if (count($list) && $uniqueRow) {
                foreach ($list as $key => $r) {
                    if (!array_diff($r, $row)) {
                        $continue = false;
                        break;
                    }
                }
            }

            if ($continue) {
                $sizeSegment = ((sizeof($list) + 2) * 100) + 61;
                $list['_' . time() . $sizeSegment . '_' . $sizeSegment] = $row;
                $list = serialize($list);

                if (!$this->saveConfig($configPath, $list, $scope, $config->getId())) {
                    throw new Exception("Something went wrong while saving the configuration value for " . $configPath);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve current website or store object.
     *
     * Store and website objects work sort of the same way, they share a lot of logic, for example when handling the
     * configuration, but for them to work we need to use the correct model, thus this function aims to return the
     * currently used store or website, whichever it is, and it should work on both front- and backend.
     *
     * @return Mage_Core_Model_Store|Mage_Core_Model_Website
     */
    public function getCurrentStoreOrWebsite()
    {
        $result = Mage::app()->getStore($this->getStoreId());

        if ($this->isAdmin()) {
            $websiteId = Mage::app()->getRequest()->getParam('website');
            $storeId = Mage::app()->getRequest()->getParam('store');

            if (!$storeId && $websiteId) {
                $result = Mage::app()->getWebsite($websiteId);
            }
        }

        return $result;
    }

    /**
     * Parse serialized link data into array (mainly for grid based tabs in administration panel).
     *
     * @param string $data
     * @return array
     */
    public function parseSerializedData($data)
    {
        return Mage::helper('adminhtml/js')->decodeGridSerializedInput($data);
    }

    /**
     * Retrieve entity type entry from eav_entity_type table.
     *
     * @param int $id
     * @param string $key
     * @return array
     */
    public function getEntityTypeData($id, $key = '')
    {
        $result = null;

        /* @var $cache MageParts_Permissions_Helper_Cache */
        $cache = $this->getCacheHelper();

        $cacheKey = $cache->generateCacheKey(array(
            'entity_type_data',
            $id
        ));

        $result = $cache->decode($cache->load($cacheKey), true);

        if (!is_array($result)) {
            $model = Mage::getModel('eav/entity_type')->load($id);

            if ($model && $model->getId()) {
                $result = $model->getData();
            } else {
                $result = array();
            }

            $cache->save($result, $cacheKey, array('eav'));
        }

        if (!empty($key) && is_array($result)) {
            $result = isset($result[$key]) ? $result[$key] : null;
        }

        return $result;
    }

    /**
     * Retrieve attribute property.
     *
     * @param string $code
     * @param array $keys
     * @return array
     */
    public function getAttributeData($type, $code, $keys = array())
    {
        $result = null;

        if (!is_array($keys)) {
            $keys = array((string) $keys);
        }

        /* @var $cache MageParts_Permissions_Helper_Cache */
        $cache = $this->getCacheHelper();

        $cacheKey = $cache->generateCacheKey(array(
            'attribute_data',
            $type,
            $code
        ));

        $result = $cache->decode($cache->load($cacheKey), true);

        if (!is_array($result)) {
            $model = Mage::getModel('eav/entity_attribute')->loadByCode($type, $code);

            if ($model && $model->getId()) {
                $result = $model->getData();
            } else {
                $result = array();
            }

            $cache->save($result, $cacheKey, array('eav'));
        }

        if (count($keys)) {
            $data = array();

            foreach ($keys as $key) {
                $data[$key] = isset($result[$key]) ? $result[$key] : null;
            }

            $result = $data;
        }

        return $result;
    }

    /**
     * Retrieve configuration default values using our own XML -> attribute mapping system.
     *
     * @param Mage_Core_Model_Abstract|string $model
     * @param array|string $attributes
     * @param boolean $attributesOnly
     * @return array
     */
    public function getConfigurationDefaults($model, $attributes = array(), $attributesOnly = false)
    {
        $result = array();

        $modelName = '';

        if (is_object($model) && $model instanceof Mage_Core_Model_Abstract) {
            $modelName = $model->getResourceName();
        } else if (is_string($model)) {
            $modelName = $model;
        }

        if (!empty($modelName)) {
            if (!is_array($attributes) && is_string($attributes)) {
                $attributes = array($attributes);
            }

            if (strpos($modelName, '/')) {
                $modelName = preg_replace('/\//', '_', $modelName);
            }

            /* @var $config Mage_Core_Model_Config_Element */
            $config = Mage::getConfig()->getNode('global/default_value_mapping/' . $modelName);

            if (is_object($config) && $config->hasChildren()) {
                $result = $config->asArray();

                if (count($result) && count($attributes)) {
                    $result = array_intersect_key($result, array_fill_keys($attributes, null));
                }

                if ($attributesOnly && is_array($result)) {
                    $result = array_keys($result);
                }
            }
        }

        return $result;
    }

    /**
     * Convert model name received from collection.
     *
     * @param Varien_Data_Collection_Db $collection
     * @return string
     */
    public function getModelNameFromCollection(Varien_Data_Collection_Db $collection)
    {
        $result = null;

        if ($collection instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) {
            $result = $collection->getResourceModelName();
        } else if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract && $collection->getEntity() && $collection->getEntity()->getTypeId()) {
            $result = $this->getEntityTypeData($collection->getEntity()->getTypeId(), 'entity_model');
        }

        return $result;
    }

    /**
     * Check if model / collection is EAV.
     *
     * @return boolean
     */
    public function isEavObject($object)
    {
        return ($object instanceof Mage_Eav_Model_Entity_Abstract || $object instanceof Mage_Eav_Model_Entity_Collection_Abstract);
    }

    /**
     * Retrieve approval status label by id.
     *
     * @param int $id
     * @param string $path
     * @param boolean $lowercase
     * @return string
     */
    public function getApprovalStatusLabel($id, $path, $lowercase=true)
    {
        $result = 'Unknown';

        $options = Mage::getSingleton('mageparts_base/system_config_source_approvalstatus')->toOptionArray(false, $path, $this->getStoreId());

        if (count($options)) {
            foreach ($options as $option) {
                if ((int) $option['value'] === $id) {
                    $result = $option['label'];
                }
            }
        }

        return $lowercase ? strtolower($result) : $result;
    }

    /**
     * Generate an approval code.
     *
     * @param Mage_Core_Model_Abstract $model
     * @return string
     */
    public function generateApprovalCode(Mage_Core_Model_Abstract $model)
    {
        $result = array(
            time(),
            $model->getId(),
            $model->getResourceName()
        );

        return sha1(serialize($result));
    }

    /**
     * Check if data has changed on an object.
     *
     * @param Mage_Core_Model_Abstract $object
     * @param array|string $properties
     * @return boolean
     */
    public function _dataHasChangedFor(Mage_Core_Model_Abstract $object, $properties)
    {
        $result = false;

        if (!is_array($properties)) {
            $properties = array((string) $properties);
        }

        if (count($properties)) {
            foreach ($properties as $p) {
                if ($object->dataHasChangedFor($p)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

}

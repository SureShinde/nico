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
 * @package    MageParts_RestrictCategories
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author 	   MageParts Crew
 */

class MageParts_RestrictCategories_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
     * system config options
     */
    const MP_RCA_ENABLED = 'restrictcategories/general/enabled';
    const MP_RCA_RESTRICT_PRODUCTS = 'restrictcategories/general/restrict_products';
    const MP_RCA_CACHE_ENABLED = 'restrictcategories/cache/enabled';

	const MP_RCA_CACHE_KEY_CUSTOMER_RELOAD_RECORDS = 'mp_rca_customer_reload_records';
    
	/**
	 * Whether or not the extension is enabled
	 *
	 * @var boolean
	 */
	protected $_enabled;
	
	/**
	 * Whether or not to restrict products in categories
	 * 
	 * @var boolean
	 */
	protected $_restrictProducts;
	
	/**
	 * Whether or not to cache restriction rules
	 * 
	 * @var boolean
	 */
	protected $_cacheRules;
	
	/**
	 * Store id
	 * 
	 * @var int
	 */
	protected $_storeId;
	
	/**
	 * Request object
	 */
	protected $_request;
	
	/**
	 * Customer Session
	 *
	 * @var Mage_Customer_Model_Session
	 */
	protected $_customerSession;
	
	/**
	 * Customer group
	 * 
	 * @var int
	 */
	protected $_customerGroup;
	
	/**
	 * Customer ID
	 * 
	 * @var int
	 */
	protected $_customerId;
	
	/**
	 * Unique classname pattern
	 *
	 * @var string
	 */
	protected $_elClassPattern = 'mp-nav-category-';
	
	
	/**
	 * Retrieve whether or not the extension is enabled
	 *
	 * @return boolean
	 */
	public function getIsEnabled()
	{
		if(is_null($this->_enabled)) {
			$this->_enabled = intval(Mage::getStoreConfig(self::MP_RCA_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_enabled;
	}
	
	/**
	 * Retrieve whether or not to restrict products in restricted categories
	 *
	 * @return boolean
	 */
	public function getRestrictProducts()
	{
		if(is_null($this->_restrictProducts)) {
			$this->_restrictProducts = intval(Mage::getStoreConfig(self::MP_RCA_RESTRICT_PRODUCTS, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_restrictProducts;
	}
	
	/**
	 * Retrieve whether or not to cache restriction rules
	 * 
	 * @return boolean
	 */
	public function getCacheRules()
	{
		if(is_null($this->_cacheRules)) {
			$this->_cacheRules = intval(Mage::getStoreConfig(self::MP_RCA_CACHE_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_cacheRules;
	}
	
	/**
	 * Retrieve restricted category and / or product ids for current customer
	 *
	 * @param string $type | 'product_ids' or 'category_ids' leave blank for both
	 * @param boolean $reloadSessionData | whether or not to reload data saved in customer session
	 * @return array
	 */
	public function getRestrictionRules($type = '', $reloadSessionData = false)
	{
		// get store id
		$storeId = $this->getStoreId();

		// get customer id
		$customerId = $this->getCustomerId();

		// result array
		$result = array(
			'category_ids'  => array(),
			'product_ids'	=> array()
		);
		
		// make sure this extension is enabled
		if (!$this->getIsEnabled()) {
			return $result;
		}
		
		// check whether or not to use cache
		$reloadSessionData = (!$reloadSessionData && !$this->getCacheRules()) ? true : $reloadSessionData;
		
		// get customer session
		$customerSession = $this->getCustomerSesion();
		
		if ($this->getCacheRules()) {
			// if categories have been modified by an administrator, we should reload the cached values in the customer sessions to amke sure they are accurate
			$sessionTimestamp = $customerSession->getLoadedMpRcaRestrictionRulesTime();
			$sessionTimestamp = !is_null($sessionTimestamp) && !empty($sessionTimestamp) ? $sessionTimestamp : 0;
			
			$cachedCategoryTimestamp = $this->getCacheData('mp_rca_last_save_time');
			
			if ($cachedCategoryTimestamp && (intval($sessionTimestamp) < intval($cachedCategoryTimestamp))) {
				$reloadSessionData = true;
			}
			
			// check if the customer have switched store view
			if (!$reloadSessionData && ($customerSession->getMpRcaLastLoadedInStoreView() != $storeId)) {
				$reloadSessionData = true;
			}

			// if the session time stamp is earlier than the last time the customer record was updated the rules stored in the session should be reloaded
			if (($reloadSessionTime = intval($this->getCustomerReloadRecords($customerId))) > $sessionTimestamp) {
				$reloadSessionData = true;
			}
		}
		
		// check if we should load rules from customers session data or not
		if (!$reloadSessionData && $this->getCacheRules()) {
			// get category ids from session data
			$result['category_ids'] = $customerSession->getRestrictedCategoryIds();
			
			// get product ids from session data
			if ($this->getRestrictProducts()) {
				$result['product_ids'] = $customerSession->getRestrictedProductIds();
			}
		}
		else {
			// get rules collection
			$collection = Mage::getModel('restrictcategories/rule')
				->getCollection()
				->addStoreFilter($storeId)
				->addCustomerGroupsToResult();
				
			// list of categories which has already been used (when loading the collection, default rules are also included, so we need to make sure we don't load anything twice)
			$used = array();
		
			if (count($collection)) {
				// get customer group id
				$customerGroup  = $this->getCustomerGroup();
				
				// avoid dbl categories
				foreach ($collection as $rule) {
					// get rule category id
					$categoryId = $rule->getCategoryId();
					
					if (!isset($used["cat_{$categoryId}"])) {
						// whether or not to restrict this category
						$restrictCategory = true;
						
						// get data from rule
						$customerGroups = $rule->getCustomerGroupId();
						$customerIds = $rule->getCustomerIds();
						
						// make sure there is any data to check against
						if (empty($customerGroups) && empty($customerIds)) {
							$restrictCategory = false;
						}
						
						// check customer group
						if ($restrictCategory && in_array($customerGroup, $customerGroups)) {
							$restrictCategory = false;
						}
						
						// check customer id
						if ($restrictCategory && !empty($customerIds)) {
							$customerIds = explode(',', $customerIds);
							
							if (in_array($customerId, $customerIds)) {
								$restrictCategory = false;
							}
						}
						
						if ($restrictCategory) {
							// set registrey flag, to avoid endless loops (load here, goes to dispatcher, which collects rules again, which goes back to dispatcher on load and so on)
		 					Mage::register('mageparts_restrictcategories_flag_loading_rules', true);
			 					
		 					// load category
			 				$category = Mage::getModel('catalog/category')
			 					->setStoreId($storeId)
			 					->load($categoryId);
	 					
			 				// add category (and children) to restriction array
			 				$childCategories = $category->getAllChildren();
			 				
			 				if (!empty($childCategories)) {
			 					$result['category_ids'] = array_merge($result['category_ids'], explode(',', $childCategories));
			 				}
			 				
			 				if ($this->getRestrictProducts()) {
				 				// get cproduct collection from category
				 				$productCollection = $category->getProductCollection();
				 				
			 					if (count($productCollection)) {
					 				foreach ($productCollection as $product) {
					 					$result['product_ids'][] = $product->getId();
					 				}
			 					}
			 				}
			 				
			 				// remove loading flag
			 				Mage::unregister('mageparts_restrictcategories_flag_loading_rules');
						}
						
						// mark category as used, so we don't load it twice (avoiding default values incase of separate values for this specific store view)
						$used["cat_{$categoryId}"] = true;
					}
				}
			}
			
			// add data to customer session
			if ($this->getCacheRules()) {
				$customerSession->setLoadedMpRcaRestrictionRulesTime(time());
				$customerSession->setMpRcaLastLoadedInStoreView($storeId);
				$customerSession->setRestrictedCategoryIds($result['category_ids']);
				$customerSession->setRestrictedProductIds($result['product_ids']);
			}
		}
		
		// return results
		if (!empty($type)) {
			return isset($result[$type]) ? $result[$type] : array();
		}
		else {
			return $result;
		}
	}
	
	/**
	 * Get customer session
	 *
	 * @return Mage_Customer_Model_Session
	 */
	public function getCustomerSesion()
	{
		if (is_null($this->_customerSession)) {
			$this->_customerSession = Mage::getSingleton('customer/session');
		}
		return $this->_customerSession;
	}
	
	/**
	 * Get the group id of the currently logged in customer, 0 "NOT LOGGED IN" is the default group retruned by this function
	 *
	 * @return int
	 */
	public function getCustomerGroup()
	{
		if(is_null($this->_customerGroup)) {
			// get customer session
			$customerSession = $this->getCustomerSesion();
			
			// check if customer is logged in
			if($customerSession->isLoggedIn()) {
				$this->_customerGroup = intval($customerSession->getCustomer()->getGroupId());
			}
			else {
				$this->_customerGroup = 0;
			}
		}
		
		return $this->_customerGroup;
	}
	
	/**
	 * Get customer id
	 *
	 * @return int
	 */
	public function getCustomerId()
	{
		if(is_null($this->_customerId)) {
			// get customer session
			$customerSession = $this->getCustomerSesion();
			
			// check if customer is logged in
			if($customerSession->isLoggedIn()) {
				$this->_customerId = intval($customerSession->getCustomer()->getId());
			}
			else {
				$this->_customerId = 0;
			}
		}
		
		return $this->_customerId;
	}
	
	/**
	 * Get current store id
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if(is_null($this->_storeId)) {
			// get router name
	    	$routeName = $this->_getRequest()->getRouteName();
	    	
	    	// check if the store parameter is available if we are in the admin area
	    	if($routeName == 'adminhtml' && $this->_getRequest()->getParam('store')) {
	    		$this->_storeId = intval($this->_getRequest()->getParam('store'));
	    	}
	    	else {
				$this->_storeId = intval(Mage::app()->getStore()->getId());
	    	}
		}
		
		return $this->_storeId;
	}
	
	/**
	 * Request object
	 */
	public function _getRequest()
	{
		if(!$this->_request) {
			$this->_request = Mage::app()->getRequest();
		}
		return $this->_request;
	}
	
	/**
	 * Version independent function for saving data in the cache
	 *
	 * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @param   int $lifeTime (in seconds)
	 */
	public function saveCacheData($data, $id, $tags=array(), $lifeTime=false)
	{
		$result = null;
		
		if ($this->getMagentoVersion() >= 1.4) {
			$result = Mage::getModel('core/cache')->save($data, $id, $tags, $lifeTime);
		}
		else {
			$result = Mage::app()->saveCache($data, $id, $tags, $lifeTime);
		}
		
		return $result;
	}
	
	/**
	 * Version idependent function for retrieving data from the cache
	 *
     * @param   string $id
     * @return  mixed
	 */
	public function getCacheData($id)
	{
		$data = null;
		
		if ($this->getMagentoVersion() >= 1.4) {
			$data = Mage::getModel('core/cache')->load($id);
		}
		else {
			$data = Mage::app()->loadCache($id);
		}
		
		return $data;
	}
	
	/**
	 * Retrieve readable version of running Magento installation
	 *
	 * @return float
	 */
	public function getMagentoVersion()
	{
		// get current magento version
    	$version = Mage::getVersion();
    	
    	// get position of first '.'
    	$pos = strpos($version,'.');
    	
    	// remove all '.' after the first one
    	$version1 = substr($version,0,$pos+1);
    	$version2 = str_replace('.','',substr($version,$pos+1));
    	
    	// parse the version number to a float number
    	$version = floatval("{$version1}{$version2}");
    	
    	return $version;
	}
	
	/**
	 * Set unique classname pattern
	 *
	 * @param string $val
	 */
	public function setElClassPattern($val)
	{
		$this->_elClassPattern = $val;
	}
	
	/**
	 * Get unique classname pattern
	 *
	 * @return string
	 */
	public function getElClassPattern()
	{
		return $this->_elClassPattern;
	}

	/**
	 * Add customer id to reload records
	 *
	 * @param int $customerId
	 */
	public function addCustomerRecordToReload($customerId)
	{
		// get current customer records
		$customerRecords = $this->getCustomerReloadRecords();

		// add new or update existing customer record with current timestamp (ie. reload time)
		$customerRecords[$customerId] = time();

		// serialize customer records data
		$customerRecords = serialize($customerRecords);

		// save updated customer records in cache
		$this->saveCacheData($customerRecords, self::MP_RCA_CACHE_KEY_CUSTOMER_RELOAD_RECORDS, array(), 9999999999);
	}

	/**
	 * Get customer reload record(s)
	 *
	 * @param int $customerId
	 */
	public function getCustomerReloadRecords($customerId = 0)
	{
		// get current customer records
		$customerRecords = $this->getCacheData(self::MP_RCA_CACHE_KEY_CUSTOMER_RELOAD_RECORDS);

		// unserialize customer records data
		$customerRecords = unserialize($customerRecords);

		// customer records must always be an array
		$customerRecords = !is_array($customerRecords) ? array() : $customerRecords;

		if (!$customerId) {
			return $customerRecords;
		}
		else {
			if (isset($customerRecords[$customerId])) {
				return $customerRecords[$customerId];
			}
		}

		// default return value
		return array();
	}
	
}
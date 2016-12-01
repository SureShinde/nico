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

class MageParts_RestrictCategories_Model_Observer
{
	
	/**
	 * Config path to error redirection page
	 */
	const ERROR_REDIRECT_PAGE = 'restrictcategories/general/error_redirect_page';
	
	
	public function beforeProductCollectionLoad($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled() || !$helper->getRestrictProducts()) {
			return;
		}
		
		// check if we should avoid dispatch (in the function to get rules we load each category, to get their product collection, so we get an endless loop as the call leads back here)
		if (Mage::registry('mageparts_restrictcategories_flag_loading_rules') == true) {
			return;
		}
		
		// get restricted categories
		$restrictedProducts = Mage::helper('restrictcategories')->getRestrictionRules('product_ids');
		
		if (count($restrictedProducts)) {
			// add product id filter to collection
			$event->getCollection()
				->addFieldToFilter('entity_id', array('nin' => $restrictedProducts));
		}
	}
	
	/**
	 * Event hooking catalog_category_load_before
	 *
	 * @param unknown_type $event
	 */
	public function beforeProductLoad($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled() || !$helper->getRestrictProducts()) {
			return;
		}
		
		// get product id		
		$productId = $event->getValue();
		
		// get restricted categories
		$restrictedProducts = $helper->getRestrictionRules('product_ids');
		
		if (in_array($productId, $restrictedProducts)) {
			// the customer has been denied access to the page, so let's redirect the customer to an error page
			$redirect = Mage::getStoreConfig(self::ERROR_REDIRECT_PAGE, $helper->getStoreId());
			
			// redirect
			Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl().$redirect);
		}
	}
	
	/**
	 * Event hooking catalog_category_load_before
	 *
	 * @param unknown_type $event
	 */
	public function beforeCategoryLoad($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled()) {
			return;
		}
		
		// check if we should avoid dispatch (in the function to get rules we load each category, to get their product collection, so we get an endless loop as the call leads back here)
		if (Mage::registry('mageparts_restrictcategories_flag_loading_rules') == true) {
			return;
		}
		
		// get category id
		$categoryId = $event->getValue();
		
		// get restricted categories
		$restrictedCategories = $helper->getRestrictionRules('category_ids');
		
		if (in_array($categoryId, $restrictedCategories)) {
			// the customer has been denied access to the page, so let's redirect the customer to an error page
			$redirect = Mage::getStoreConfig(self::ERROR_REDIRECT_PAGE, $helper->getStoreId());
			
			// redirect
			Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl().$redirect);
		}
	}
	
	/**
	 * Event hooking mageparts_restrictcategories_observer
	 * 
	 * This function will save the categories restriction settings, it's run after a category has been saved
	 *
	 * @param unknown_type $event
	 */
	public function saveRestrictCategoryRules($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled()) {
			return;
		}
		
		// we have updated our categories, so let's cache the latest timestamp for these updates, making sure that all customer sessions accuires the new data
		$helper->saveCacheData(time(), 'mp_rca_last_save_time', array(), 9999999999);
		
		// get category
		$category = $event->getCategory();
		
		// make sure that the category has an ID before we save the restriction rules
		if (!$category->getId()) {
			return;
		}
		
		// get restrictions post data
		if ($data = $category->getRestrictions()) {
			// check if we should remove the rule or not
			if (isset($data['rule_id']) && ((isset($data['remove_rule']) && intval($data['remove_rule']) == 1) || (isset($data['use_default_values']) && intval($data['use_default_values']) == 1))) {
				// load rule
				$rule = Mage::getModel('restrictcategories/rule')->load($data['rule_id']);
				
				// delete loaded rule
				if ($rule->getId()) {
					$rule->delete();
					return;
				}
			}
			else {
				// save rule
				$data['category_id'] = $category->getId();
				$data['store_id'] = $helper->getStoreId();
				
				Mage::getModel('restrictcategories/rule')
					->setData($data)
					->save();
			}
		}
	}
	
	/**
	 * Reload customer session data
	 */
	public function resetCustomerSessionData($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled()) {
			return;
		}
		
		// we reset the timestamp of when the restriction data was last set on the customer session, thus forcing it to reload when the page is refreshed
		$helper->getCustomerSesion()->setLoadedMpRcaRestrictionRulesTime(null);
	}
	
	/**
	 * Add restriction tab to category page
	 *
	 * @param unknown_type $event
	 */
	public function addRestrictionsTab($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');
		
		// check if extension is enabled
		if (!$helper->getIsEnabled()) {
			return;
		}
		
		$event->getTabs()->addTab('restrictions', array(
            'label'     => $helper->__('Restrictions'),
            'content'   => $event->getTabs()->getLayout()->createBlock('restrictcategories/adminhtml_catalog_category_edit_tab_restrictions', 'category.restrictions')->toHtml(),
        ));
	}

	/**
	 * Runs after a customer record has been saved
	 *
	 * Event: customer_customer_save_after
	 */
	public function saveCustomerRecord($event)
	{
		// get helper object
		$helper = Mage::helper('restrictcategories');

		// check if extension is enabled
		if (!$helper->getIsEnabled()) {
			return;
		}

		$customerId = intval($event->getCustomer()->getId());

		if ($customerId) {
			// add customer id to reload record
			$helper->addCustomerRecordToReload($customerId);
		}
	}
	
}
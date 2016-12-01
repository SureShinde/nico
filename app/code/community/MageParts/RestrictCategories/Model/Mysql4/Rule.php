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

class MageParts_RestrictCategories_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('restrictcategories/rule', 'rule_id');
    }
    
    /**
     * Save store and customer group information related to the rule
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    	// get rule id
    	$ruleId = $object->getId();
    	
    	if (!empty($ruleId)) {
	    	// setup select condition
	    	$condition = $this->_getWriteAdapter()->quoteInto('rule_id = ?', $ruleId);
	        
	        /* STORE RELATIONS */
	        // remove odl store relations
	        $this->_getWriteAdapter()->delete($this->getTable('restrictcategories/rule_store'), $condition);
	        
	        // add new store relations
	        $storeId = $object->getData('store_id') ? $object->getData('store_id') : 0;
	        
	        $this->_getWriteAdapter()->insert($this->getTable('restrictcategories/rule_store'), array(
	        	'rule_id' 	=> $ruleId,
	        	'store_id' 	=> $storeId
	        ));
	        
	        /* CUSTOMER GROUP RELATIONS */
	        // remove current customer group relations
	        $this->_getWriteAdapter()->delete($this->getTable('restrictcategories/rule_customer_group'), $condition);
	
	        // add new customer group relations
	        foreach ((array)$object->getData('customer_groups') as $customerGroup) {
	            $customerGroupArray = array();
	            $customerGroupArray['rule_id'] = $ruleId;
	            $customerGroupArray['customer_group_id'] = $customerGroup;
	            $this->_getWriteAdapter()->insert($this->getTable('restrictcategories/rule_customer_group'), $customerGroupArray);
	        }
    	}

        return parent::_afterSave($object);
    }
    
    /**
     * Add store and customer group data to collection
     * 
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
    	
    	// add store id to select
        $storeSelect = $this->_getReadAdapter()->select()
            ->from($this->getTable('restrictcategories/rule_store'))
            ->where('rule_id = ?', $object->getId())
            ->order('store_id DESC')
			->limit(1);

		$storeId = 0;
     	
        if ($storeData = $this->_getReadAdapter()->fetchRow($storeSelect)) {
        	$storeId = $storeData['store_id'];
        }
        
        $object->setData('store_id', $storeId);	

        // add customer group id to select
        $customerGroupSelect = $this->_getReadAdapter()->select()
        	->from($this->getTable('restrictcategories/rule_customer_group'))
            ->where('rule_id = ?', $object->getId());
        
        $customerGroupArray = array();
        
       	if ($customerGroupData = $this->_getReadAdapter()->fetchAll($customerGroupSelect)) {
        	if (count($customerGroupData)) {
	            foreach ($customerGroupData as $row) {
	                $customerGroupArray[] = $row['customer_group_id'];
	            }
        	}
        }
        
        $object->setData('customer_group_id', $customerGroupArray);
        
        return parent::_afterLoad($object);
    }
    
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
    	// get select object
        $select = parent::_getLoadSelect($field, $value, $object);

        // get store id
        $storeId = Mage::helper('restrictcategories')->getStoreId();
    
		// add store filter to select statement to limit the number of returned rules
		$select->join(
			array('rcars' => $this->getTable('restrictcategories/rule_store')),
			$this->getMainTable().'.rule_id = `rcars`.rule_id'
		)
		->where('`rcars`.store_id IN (0, ?) ', $storeId)
		->order('store_id DESC')
		->limit(1);
		
		// return select statement
        return $select;
    }
    
}
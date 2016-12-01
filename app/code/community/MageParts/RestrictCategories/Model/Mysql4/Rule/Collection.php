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

class MageParts_RestrictCategories_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('restrictcategories/rule');
        
        // map rule id field
    	$this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }
    
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return MageParts_RestrictCategories_Model_Mysql4_Rule_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
    	// check if filter is already applied
        if (!$this->getFlag('mageparts_rca_store_filter_added')) {
        	// get store id from model
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            // add store filter to select statement
            $this->getSelect()->join(
                array('store_table' => $this->getTable('restrictcategories/rule_store')),
                'main_table.rule_id = store_table.rule_id',
                array('store_id')
            )
            ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store))
            ->order('store_id DESC');

            $this->setFlag('mageparts_rca_store_filter_added', true);
        }

        return $this;
    }
    
    /**
     * Add customer groups to collection
     * 
     * @param customer group filter
     * @return MageParts_RestrictCategories_Model_Mysql4_Rule_Collection
     */
    public function addCustomerGroupsToResult()
    {
    	foreach ($this as $rule) {
    		// generate select statement
    		$select = $this->getConnection()->select()
	            ->from(array('cg_table' => $this->getTable('restrictcategories/rule_customer_group')))
	            ->join(
	            	array('main_table' => $this->getResource()->getTable('restrictcategories/rule')),
	                'main_table.rule_id = cg_table.rule_id',
	                array('cg_table.customer_group_id'))
	            ->where($this->getConnection()->quoteInto(
	                'cg_table.rule_id = ?', $rule->getId())
	            );
	        
	        // execute select statement
        	$rs = $this->getConnection()->fetchAll($select);
        
        	$data = array();
        	
        	if (count($rs)) {
	        	foreach ($rs as $record) {
	        		if (isset($record['customer_group_id'])) {
	        			$data[] = intval($record['customer_group_id']);
	        		}
	        	}
        	}
        	
        	// add customer group data to rule
    		$rule->setCustomerGroupId($data);
    	}
    	
    	return $this;
    }
    
    /**
     * Add an attribute to the collection filter
     *
     * @param string $field
     * @param string $value
     * @return MageParts_RestrictCategories_Model_Mysql4_Rule_Collection
     */
    public function addAttributeToFilter( $field, $value )
    {
    	if(!empty($field) && !empty($value)) {
    		$this->getSelect()->where("{$field} = ?",$value);
    	}
    	return $this;
    }
    
    /**
     * Set collection size limit
     *
     * @param int $limit
     * @return MageParts_RestrictCategories_Model_Mysql4__Rule_Collection
     */
    public function setSizeLimit( $limit )
    {
    	if(!empty($limit)) {
    		$this->getSelect()->limit($limit);
    	}
    	return $this;
    }
    
}
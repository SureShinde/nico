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

class MageParts_RestrictCategories_Block_Adminhtml_Catalog_Category_Edit_Tab_Restrictions extends Mage_Adminhtml_Block_Catalog_Form
{
	
	/**
	 * Category object container
	 *
	 * @var Mage_Catalog_Model_Category
	 */
	protected $_category;
	
	/**
	 * Restriction rule object container
	 *
	 * @var MageParts_RestrictCategories_Model_Rule
	 */
	protected $_rule;

	/**
	 * Constructor
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    	$this->setTemplate('mageparts/restrictcategories/tab.phtml');
    }

    /**
     * Get category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }
    
    /**
     * Get rule object
     *
     * @return MageParts_RestrictCategories_Model_Rule
     */
    public function getRule()
    {
    	if (!$this->_rule) {
    		$this->_rule = Mage::getModel('restrictcategories/rule')->load($this->getCategory()->getId(), 'category_id');
    	}
    	return $this->_rule;
    }

    /**
     * Prepare form layout
     */
    public function _prepareForm()
    {
    	// get helper object
    	$helper = Mage::helper('restrictcategories');
    	
    	// get working store view
    	$workingStoreId = $helper->getStoreId();
    	
    	// get rule object
    	$rule = $this->getRule();
    	
    	// whether or not elements are disabled
    	$elementsDisabled = (($rule->getStoreId() == $workingStoreId) || $workingStoreId == 0) ? false : true;
    	
        parent::_prepareLayout();

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('restrictions_');

        $fieldset = $form->addFieldset('category_restrictions', array('legend'=>$helper->__('Restriction Information')));
        
        // customer group id
        $fieldset->addField('customer_group_id', 'multiselect', array(
            'name'      => 'restrictions[customer_groups][]',
            'label'     => $helper->__('Allowed Customer Groups'),
            'title'     => $helper->__('Allowed Customer Groups'),
            'required'  => false,
            'values'    => Mage::getModel('customer/group')->getCollection()->toOptionArray(),
            'disabled'	=> $elementsDisabled
        ));
        
        // customer ids
        $fieldset->addField('customer_ids', 'text', array(
            'name'      => 'restrictions[customer_ids]',
            'label'     => $helper->__('Allowed Customers'),
            'title'     => $helper->__('Allowed Customers'),
            'required'  => false,
            'disabled'	=> $elementsDisabled
        ));
        
        // remove rule field, for easy rule removal
        if ($rule->getId() && ($rule->getStoreId() == $workingStoreId)) {
	     	$fieldset->addField('remove_rule', 'checkbox', array(
	            'name'      => 'restrictions[remove_rule]',
	            'label'     => $helper->__('Remove Restriction Rule'),
	            'title'     => $helper->__('Remove Restriction Rule'),
	            'value'		=> '1',
	            'required'  => false
	        ));
        }
       
        // add this for all store views except the option "All Store Views" (ie. default values)
        if ($workingStoreId > 0) {
	     	$fieldset->addField('use_default_values', 'checkbox', array(
	            'name'      => 'restrictions[use_default_values]',
	            'label'     => $helper->__('Use Default Values'),
	            'title'     => $helper->__('Use Default Values'),
	            'value'		=> '1',
	            'required'  => false,
	            'checked'	=> $elementsDisabled,
	            'onclick'	=> 'mpRestrictCategories.toggleDefaultValues(this);'
	        ));
        }
        
        // rule id
        if($rule->getId()) {
        	if($rule->getStoreId() == $workingStoreId) {
		        // rule id
		        $fieldset->addField('rule_id', 'hidden', array(
		            'name' => 'restrictions[rule_id]'
		        ));
        	}
        }

        $form->addValues($rule->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
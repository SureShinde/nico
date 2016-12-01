<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class MW_Helpdesk_Model_Helpdeskrule_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract
{
	public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = array(
                'string'      => array('==', '!=', '{}', '!{}', '()', '!()', 'REGEXP'),
                'numeric'     => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
                'date'        => array('==', '>=', '<='),
                'select'      => array('==', '!='),
                'boolean'     => array('==', '!='),
                'multiselect' => array('{}', '!{}', '()', '!()'),
                'grid'        => array('()', '!()'),
				'message'     => array('{}', '!{}', 'REGEXP'),
            );
            $this->_arrayInputTypes = array('multiselect', 'grid');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Default operator options getter
     * Provides all possible operator options
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = array(
                '=='  => Mage::helper('rule')->__('is'),
                '!='  => Mage::helper('rule')->__('is not'),
                '>='  => Mage::helper('rule')->__('equals or greater than'),
                '<='  => Mage::helper('rule')->__('equals or less than'),
                '>'   => Mage::helper('rule')->__('greater than'),
                '<'   => Mage::helper('rule')->__('less than'),
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),
                '()'  => Mage::helper('rule')->__('is one of'),
                '!()' => Mage::helper('rule')->__('is not one of'),
            	'REGEXP' => Mage::helper('rule')->__('regular expression')
            );
        }
        return $this->_defaultOperatorOptions;
    }
	
    public function loadAttributeOptions()
    {
        $attributes = array(
        	'sender' => Mage::helper('salesrule')->__('Customer\'s Email'),
        	'member_id' => Mage::helper('helpdesk')->__('Staff\'s Email'),
        	'email' => Mage::helper('helpdesk')->__('Gateway\'s Email'),
            'subject' => Mage::helper('helpdesk')->__('Subject'),
            'content' => Mage::helper('helpdesk')->__('Message'),
        	'date(created_time)' => Mage::helper('helpdesk')->__('Created Time'),
        	'date(last_reply_time)' => Mage::helper('helpdesk')->__('Updated Time'),
        	'increment_id' => Mage::helper('helpdesk')->__('Order No'),
        	//'item_id' => Mage::helper('helpdesk')->__('Item No'),
        	//'email_ref_id' => Mage::helper('helpdesk')->__('eBay Ref'),
        	'status' => Mage::helper('helpdesk')->__('Ticket Status'),
        	'priority' => Mage::helper('helpdesk')->__('Ticket Priority'),
        
//            'base_subtotal' => Mage::helper('salesrule')->__('Subtotal'),
//            'total_qty' => Mage::helper('salesrule')->__('Total Items Quantity'),
//            'weight' => Mage::helper('salesrule')->__('Total Weight'),
//            'payment_method' => Mage::helper('salesrule')->__('Payment Method'),
//            'shipping_method' => Mage::helper('salesrule')->__('Shipping Method'),
//            'postcode' => Mage::helper('salesrule')->__('Shipping Postcode'),
//            'region' => Mage::helper('salesrule')->__('Shipping Region'),
//            'region_id' => Mage::helper('salesrule')->__('Shipping State/Province'),
//            'country_id' => Mage::helper('salesrule')->__('Shipping Country'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal': case 'weight': case 'total_qty':
                return 'numeric';

            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id': case 'status': case 'priority':
                return 'select';
            	
            case 'date(created_time)': case 'date(last_reply_time)':
            	return 'date';
				
			case 'content':
            	return 'message';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id': case 'status': case 'priority':
                return 'select';
            case 'date(created_time)': case 'date(last_reply_time)':
            	return 'date';
        }
        return 'text';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;
                
                case 'status':
                    $options = Mage::getModel('helpdesk/config_source_status')
                        ->toOptionArray();
                    break;
                    
               case 'priority':
                    $options = Mage::getModel('helpdesk/config_source_priority')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
                        ->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')
                        ->toOptionArray();
                    break;

                case 'payment_method':
                    $options = Mage::getModel('adminhtml/system_config_source_payment_allmethods')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */ 
    public function validate(Varien_Object $object)
    {
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && ! $address->hasPaymentMethod()) {
            $address->setPaymentMethod($object->getQuote()->getPayment()->getMethod());
        }

        return parent::validate($address);
    }
}

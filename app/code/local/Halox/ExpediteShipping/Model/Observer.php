<?php

class Halox_ExpediteShipping_Model_Observer extends Varien_Object
{
    private $_expediteCarrierRate = null;
    private $_debugMode;

    
	protected function _getCurrentStore()
	{
		return Mage::app()->getStore();
	}

	protected function _getHelper()
	{
		return Mage::helper('halox_expediteshipping');
	}

    protected function _isDebugMode()
    {
        if(!$this->_debugMode){
            $storeId = $this->_getCurrentStore()->getId();
            $this->_debugMode = $this->_getHelper()->isDebugModeEnabled($storeId);    
        }
        return $this->_debugMode;
    }

	protected function _getShippingInstance()
	{
		return Mage::getModel('shipping/shipping');
	}
    
    /**
     * @see Mage_Shipping_Model_Shipping::collectCarrierRates() method
     */
    protected function _collectExpediteCarrierRates($carrierCode, $request)
    {
        
        $shippingInstance = $this->_getShippingInstance();
        
        $carrier = $shippingInstance->getCarrierByCode($carrierCode, $request->getStoreId());
        
        if (!$carrier) {
            return null;
        }
        
        if ($carrier->getConfigData('shipment_requesttype')) {
            $packages = $shippingInstance->composePackagesForCarrier($carrier, $request);
            if (!empty($packages)) {
                $sumResults = array();
                foreach ($packages as $weight => $packageCount) {
                    //clone carrier for multi-requests
                    $carrierObj = clone $carrier;
                    $request->setPackageWeight($weight);
                    $result = $carrierObj->collectRates($request);
                    if (!$result) {
                        return null;
                    } else {
                        $result->updateRatePrice($packageCount);
                    }
                    $sumResults[] = $result;
                }
                if (!empty($sumResults) && count($sumResults) > 1) {
                    $result = array();
                    foreach ($sumResults as $res) {
                        if (empty($result)) {
                            $result = $res;
                            continue;
                        }
                        foreach ($res->getAllRates() as $method) {
                            foreach ($result->getAllRates() as $resultMethod) {
                                if ($method->getMethod() == $resultMethod->getMethod()) {
                                    $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                    continue;
                                }
                            }
                        }
                    }
                }
            } else {
                $result = $carrier->collectRates($request);
            }
        } else {
            $result = $carrier->collectRates($request);
        }
        
        if (!$result) {
            return null;
        }
            
        if ($result->getError()) {
            return null;
        }
        
        return $result;
    }

	/**
	 * provides an alternate implementation of 
	 * Mage_Shipping_Model_Shipping::collectCarrierRates() to skip the allowed method checks
	 *
	 * @return Mage_Sales_Model_Quote_Address_Rate object
	 */
	protected function _getExpediteCostFromCarrierDirectly($quoteAddress, $method = array())
	{
		
		if(!isset($method['carrier']) || !isset($method['method'])){
            return null;
        }
        
        $shippingRequest = $this->_getHelper()->buildShippingRequest($quoteAddress);

		//bypass all validation of shipping methods like whether they are enabled or allowed for countries
        $result = $this->_collectExpediteCarrierRates($method['carrier'], $shippingRequest);
		
		if(!$result){
			Mage::log(__METHOD__ . ':: No results returned for the selected expedite shipping method.', Zend_Log::ERR, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
			return null;	
		}
		
		$rates = $result->getAllRates();

		foreach($rates as $rate){
			if($rate->getMethod() == $method['method'] && $rate->getCarrier() == $method['carrier']){
				return $rate;
			}
		}

		return null;
	}

	/**
	 * get the pre-calculated rate from shipping address rate collection object.
	 * if not found do a API call directly to the expedite shipping method
	 *
	 * @todo override 
	 */
	protected function _getExpediteShippingCarrierRateByCode($quoteAddress, $code)
	{
		
		if(! $this->_expediteCarrierRate){
            
            $expediteCarrier = preg_match('/([a-zA-Z]+)_(\w+)/', $code, $carrierAndCode);
            if(!is_array($carrierAndCode)){
                Mage::log(__METHOD__ . ':: Invalid shipping method code for expedite shipping found.', Zend_Log::ERR, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                return null;
            }

            $expediteCarrierCode = $carrierAndCode[1];
            $expediteCarrierMethodCode = $carrierAndCode[2];	

            $this->_expediteCarrierRate = $this->_getExpediteCostFromCarrierDirectly($quoteAddress, array(
                'carrier' => $expediteCarrierCode, 
                'method' => $expediteCarrierMethodCode
            ));
            
        }
        
        return $this->_expediteCarrierRate;
	}

	/**
	 * deduct markup from the expedite cost before deducting it to other shipping rates
     * @param expediteCost original cost of the base shipping method without handling fees
	 */
	protected function _applyMarkup($expediteCost, $storeId)
	{	

		$markupType = $this->_getHelper()->getMarkupType($storeId);
		$markupAmount = (float)$this->_getHelper()->getMarkupAmount($storeId);
		
		if($this->_isDebugMode()){
            Mage::log('Mark up Type:: ' . $markupType, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            Mage::log('Mark up Amount:: ' . $markupAmount, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }

        $result = 0.00;

        switch($markupType){
			case Halox_ExpediteShipping_Model_Adminhtml_System_Config_Source_MarkupType::MARKUP_TYPE_FIXED:
				$result = $expediteCost + $markupAmount;
                break;
			case Halox_ExpediteShipping_Model_Adminhtml_System_Config_Source_MarkupType::MARKUP_TYPE_PERCENTAGE:
				$result = $expediteCost + ($expediteCost * ($markupAmount / 100));
                break;
			default:
				$result = $expediteCost;	
		}

        return $result;
	}

	/**
	 * reduce the costs of all other shipping methods by expedite cost and hide those rates which are zeroed.
	 */
	protected function _adjustShippingRatesForQuoteAddress($quoteAddress)
	{
		if($this->_isDebugMode()){
            Mage::log(__METHOD__ . ' STARTS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }

        $rates = $quoteAddress->getShippingRatesCollection();
		if($rates->getSize() <= 0){
			
            if($this->_isDebugMode()){
                Mage::log('quote address do not have rates calculated. Can\'t apply expedite shipping. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            return $this;
		}
        
        $expediteCost = $quoteAddress->getExpediteCost();
        
        $storeId = $quoteAddress->getQuote()->getStoreId();
		
        foreach($rates as $rate){
			
			//method cost without handling fees
            $originalCost = $rate->getExpediteOrigCost();
            
            if($this->_isDebugMode()){
                Mage::log($rate->getCode() . ' Original Cost without handling fees:: ' . $originalCost, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
            
            $newCost = $originalCost - $expediteCost;
            
            //few shipping method rates can have -ve or zero value.
            if($newCost <= 0){
                
                if($this->_isDebugMode()){
                    Mage::log('New cost is less than zero for ' . $rate->getCode() . 'Original Cost:: ' . $rate->getCost() . ' is less than the expedite cost.', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                $newCost = 0.00;
            }
            
            $applyMarkup = $this->_getHelper()->canApplyMarkup($storeId);

            if($this->_isDebugMode()){
                Mage::log('Mark up setting:: ' . $applyMarkup, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            //apply markup before handling fees
            if($applyMarkup ){
                $newCost = $this->_applyMarkup($newCost, $storeId);
            }

            if($this->_isDebugMode()){
                Mage::log('Final Expedite Cost after Markup:: ' . $newCost, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
            
            //apply handling fees
            $newPrice = $this->_applyHandlingFees($rate, $newCost, $storeId);
            
            $rate->setPrice($newPrice);

            if($this->_isDebugMode()){
                Mage::log($rate->getCode() . ' New Price with handling fees applied:: ' . $rate->getPrice(), Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
            
            //update shipping total as per the expedite shipping calculated for the selected shipping method
            if($rate->getCode() == $quoteAddress->getShippingMethod()){
                
                $amountPrice = $quoteAddress->getQuote()->getStore()->convertPrice($rate->getPrice(), false);
                
                if($this->_isDebugMode()){
                    Mage::log('Applied Shipping method:: ' . $quoteAddress->getShippingMethod(), Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                    Mage::log('New Shipping Amount Total:: ' . $amountPrice, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                $quoteAddress->setTotalAmount('shipping', $amountPrice);
                $quoteAddress->setBaseTotalAmount('shipping', $amountPrice);
                
            }
		}
        
        Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        
        return $this;
	}
    
    /**
     * whether the cart is eligible for free shipping method
     */
    protected function _isFreeShippingAvailable($quoteAddress)
    {
        $rates = $quoteAddress->getShippingRatesCollection();
        foreach($rates as $rate){
            $code = $rate->getCode();
            if(strpos($code, 'freeshipping') !== false){
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * calculate and apply handling fees based on the new cost for shipping method
     */
    protected function _applyHandlingFees($rate, $cost, $storeId)
    {
        $carrierInstance = $this->_getShippingInstance()->getCarrierByCode($rate->getCarrier(), $storeId);
        
        return $carrierInstance->getFinalPriceWithHandlingFee($cost);
    }

	/**
	 * request expedite carrier method cost add up with surcharge if any
	 * set change all other shipping rates according to that cost
	 */
	public function onQuoteAddressCollectTotalsAfter($observer)
	{
		if($this->_isDebugMode()){
            Mage::log(__METHOD__ . ' STARTS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }

        if($this->_getCurrentStore()->isAdmin()){
			
            if($this->_isDebugMode()){
                Mage::log('Store is not front-end store. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            return $this;
		}

		$store = $this->_getCurrentStore();

		if(!$this->_getHelper()->isModuleActive($store->getId())){
			
            if($this->_isDebugMode()){
                Mage::log('Module is not active for current store. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            return $this;
		}

        $quoteAddress = $observer->getQuoteAddress();
		if($quoteAddress->getAddressType() != Mage_Sales_Model_Quote_Address::TYPE_SHIPPING){
			
            if($this->_isDebugMode()){
                Mage::log('Current quote address is not shipping address. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            return $this;
		}

        if(! $this->_isFreeShippingAvailable($quoteAddress)){
            if($this->_isDebugMode()){
                Mage::log('free shipping available check failed. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
            
            return $this;
        }

		$shippingMethodCode = $this->_getHelper()->getBaseExpediteShippingMethod($store->getId());
		if(!$shippingMethodCode){
			if($this->_isDebugMode()){
                Mage::log('Base Expedite Shipping Method not found. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
            return $this;
		}

        if($this->_isDebugMode()){
            Mage::log('Base Expedite Shipping Method Code:: ' . $shippingMethodCode, Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
        }

		$expediteCarrierRate = $this->_getExpediteShippingCarrierRateByCode($quoteAddress, $shippingMethodCode);
		if(! $expediteCarrierRate instanceof Mage_Shipping_Model_Rate_Result_Method){
			
            if($this->_isDebugMode()){
                Mage::log('Could not found the rate for base expedite shipping method. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

			return $this;
		}

        if($this->_isDebugMode()){
            Mage::log('Base Expedite Shipping Method Rate::', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
            Mage::log($expediteCarrierRate->getData(), Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
        }        
		
		$recollectTotals = false;
        
        if(!$quoteAddress->hasExpediteCost()){
            $quoteAddress->setExpediteCost($expediteCarrierRate->getCost());
            $recollectTotals = true;
        }
        
        //making sure that the rate collect is always imported to shipping address object
        $quoteAddress->setCollectShippingRates(true)->collectShippingRates()->save();
        
        $this->_adjustShippingRatesForQuoteAddress($quoteAddress);
        
        //if a shipping method is selected and shipping totals have been changed then re-collect all totals again
        if($quoteAddress->getShippingMethod() && $recollectTotals){
            
            $quoteAddress->setBaseGrandTotal(0)
                ->setGrandTotal(0)
                ->collectTotals()
                ->save();
        }
		
        if($this->_isDebugMode()){
            Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }
        
        return $this;

	}
    
    /**
     * collect shipping rates early to be able to work on them for deducting the expedite 
     * shipping
     */
    public function onCoreBlockToHtmlBefore($observer)
    {
        
        if($this->_isDebugMode()){
            Mage::log(__METHOD__ . ' STARTS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }

        $store = $this->_getCurrentStore();

		if(!$this->_getHelper()->isModuleActive($store->getId())){
			
            if($this->_isDebugMode()){
                Mage::log('Module is not active for current store. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }

            return $this;
		}

        $block = $observer->getBlock();

        if($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available){
            
            $shippingAddress = $block->getAddress();

            if($this->_isDebugMode()){
                Mage::log(get_class($shippingAddress), Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
            }
        
            if(! $shippingAddress instanceof Mage_Sales_Model_Quote_Address){
                
                if($this->_isDebugMode()){
                    Mage::log('Address is not a shipping address object. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                    Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                return $this;
            }
            
            if(! $this->_isFreeShippingAvailable($shippingAddress)){
                
                if($this->_isDebugMode()){
                    Mage::log('free shipping available check failed. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                    Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                return $this;
            }
            
            //let magento calculate the rates first
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->save();
            
            $shippingMethodCode = $this->_getHelper()->getBaseExpediteShippingMethod($store->getId());
            if(!$shippingMethodCode){
                
                if($this->_isDebugMode()){
                    Mage::log('Base Expedite Shipping Method not found. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
                    Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                return $this;
            }
            
            $expediteCarrierRate = $this->_getExpediteShippingCarrierRateByCode($shippingAddress, $shippingMethodCode);
            
            if(! $expediteCarrierRate instanceof Mage_Shipping_Model_Rate_Result_Method){
                
                if($this->_isDebugMode()){
                    Mage::log('Could not found the rate for base expedite shipping method. Exiting..', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
                    Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
                }

                return $this;
            }

            if($this->_isDebugMode()){
                Mage::log('Base Expedite Shipping Method Rate::', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
                Mage::log($expediteCarrierRate->getData(), Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);       
            }
            
            $shippingAddress->setExpediteCost($expediteCarrierRate->getCost());
            
            $this->_adjustShippingRatesForQuoteAddress($shippingAddress);
            
        }
        
        if($this->_isDebugMode()){
            Mage::log(__METHOD__ . ' ENDS', Zend_Log::DEBUG, Halox_ExpediteShipping_Helper_Data::LOG_FILE);
        }
        
        return $this;
    }
    
    

}
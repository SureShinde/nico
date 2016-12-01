<?php

class Halox_AgeVerification_Helper_Data extends Mage_Core_Helper_Abstract {
    /*
     * State collection for Age Verification Manager after selecting countries
     */

    public function getStates() {
        $statesArr = array();
        $regionColl = Mage::getModel('directory/region')->getResourceCollection()->addFieldToFilter('country_id', 'US')->getData();
        $statesArr[0] = array('value' => '*', 'label' => '*');
        foreach ($regionColl as $key => $value) {
            $arr = array('value' => $value['code'], 'label' => $value['name']);
            $statesArr[] = $arr;
        }
        return $statesArr;
    }

    /*
     * State collection for Age Verification Manager before selecting countries
     */

    public function getStateCollection() {
        $regionColl = Mage::getModel('directory/region')->getResourceCollection()->addFieldToFilter('country_id', 'US')->getData();
        end($regionColl);         // move the internal pointer to the end of the array
        $key = key($regionColl);
        $regionColl[$key + 1]['code'] = '*';
        $regionColl[$key + 1]['name'] = '*';
        $code = $this->getArrayColumn($regionColl, 'code');
        $name = $this->getArrayColumn($regionColl, 'name');
        $statesArr = array_combine($code, $name);
        return $statesArr;
    }

    /*
     * State collection of countries for Age Verification Manager
     */

    public function getCountryCollection() {
        $regionColl = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        $code = $this->getArrayColumn($regionColl, 'value');
        $name = $this->getArrayColumn($regionColl, 'label');
        $countryArr = array_combine($code, $name);
        return $countryArr;
    }

    /*
     * Method get shipping details entered on checkout onepage
     */

    public function getShippingDetails() {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $shipping = array();

        $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        $shipping['firstname'] = $address->getFirstname();
        $shipping['lastname'] = $address->getLastname();
        $shipping['company'] = $address->getCompany();
        $shipping['city'] = $address->getCity();
        $shipping['postcode'] = $address->getPostcode();
        $shipping['address'] = $address->getStreet();
        $shipping['region'] = $address->getRegion();
        $shipping['regionId'] = $address->getRegionId();
        $shipping['country'] = $address->getCountryId();
        $shipping['telephone'] = $address->getTelephone();
        $shipping['fax'] = $address->getFax();
        return $shipping;
    }

    /*
     * Get Shipping region
     */

    public function getShippingRegion() {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $shipping = array();
        $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        $shipping['regionId'] = $address->getRegionId();
        return $shipping['regionId'];
    }

    /**
     * 
     * Method checks whether customer DOB is verified or not
     */
    public function isCustomerDOBVerified($customerId = '') {
        if ($customerId == '') {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $value = $customer->getData('dob');
        if (isset($value) && !empty($value)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 
     * Checks whether Age Verification Module is enabled from config settings
     */
    public function isAgeVerifiedEnable() {
        $storeId = Mage::app()->getStore()->getStoreId();

        return Mage::getStoreConfig('age_verify_general/ageverification_options/is_age_ageverification_enabled', $storeId);
    }

    /**
     * 
      Method returns age of the region
     */
    public function getAgeByregionId($regionId) {
        $region = Mage::getModel('directory/region')->load($regionId);
        $state_code = $region->getCode();
        $coll = Mage::getModel('ageverification/ageverification')->getCollection()->addFieldToFilter('state', array($state_code, '*'));
        if ($coll->count() > 1) {
            $ageArr = $this->getArrayColumn($coll->getData(), 'age');
            return min($ageArr);
        } else {
            $age = $coll->getFirstItem()->getAge();
            return $age;
        }
    }

    /**
     * Set customer verified if customer is passed from API.
     */
    public function setCustomerVerified($customerId = '') {
        if ($customerId == '') {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customer->setData('isVerified', 1)->save();
    }

    /**
     * 
     * Checks if Customer passed the Age Verification
     */
    public function isPassedFromAPI($customerId = '') {
        if ($customerId == '') {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $rec = Mage::getModel('ageverification/ageverificationdetails')->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('quote_id', $quoteId)
                ->setOrder('date', 'DESC');
        $apiData = $this->getArrayColumn($rec->getData(), 'api_response');
        foreach ($apiData as $value) {
            $value = json_decode($value);
            if ($value->result->action == 'PASS') {
                return 1;
            }
        }
        return 0;
    }

    /**
     * 
     * Returns passes step of verification e.g. 1,2 or 3
     * 1 stands for DOB
     * 2 stands for SSN
     * 3 stands for Uploaded document
     */
    public function getPassedStepFromAPI() {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if ($customerId) {
            $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
            $rec = Mage::getModel('ageverification/ageverificationdetails')->getCollection()->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('quote_id', $quote_id)->setOrder('date', 'DESC');
            $apiData = $this->getArrayColumn($rec->getData(), 'api_response');
            $resArr = $rec->getData();
        } else {
            $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
            $verifyData = Mage::getModel('sales/quote')->load($quote_id)->getVerificationData();
            $rec = unserialize($verifyData);
            $apiData = $this->getArrayColumn($rec, 'api_response');
            $resArr = $rec;
        }

        foreach ($apiData as $key => $value) {
            $value = json_decode($value);
            if ($value->result->action == 'PASS') {
                return $resArr[$key]['ageverification_step'];
            } else {
                if ($resArr[$key]['ageverification_step'] == 3)
                    return $resArr[$key]['ageverification_step'];
            }
        }
        return 0;
    }

    /**
     * 
     * definition of array_column predefined function
     */
    public function getArrayColumn($dataArr, $index) {
        $output = array();
        if (is_array($dataArr)) {
            foreach ($dataArr as $arrvalue) {
                foreach ($arrvalue as $key => $value) {
                    if ($key == $index) {
                        $output[] = $value;
                    }
                }
            }
        }
        return $output;
    }

    /**
     * 
     * Set customer DOB in backend customer module if customer is verified from API
     */
    public function setCustomerDOB($data, $customerId = '', $customerType = 'apiPassed', $orderObj = null) {
        if ($customerId == '') {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        try{
        $data['step'] = isset($data['step']) ? $data['step'] : $data['ageverification_step'];
        if ($customerType == 'apiPassed') {
            if ($data['step'] == 1) {
                $dob = $data['month'] . '/' . $data['day'] . '/' . $data['year'];
                $day = $data['day'];
                $month = $data['month'];
                $year = $data['year'];
            } else if ($data['step'] == 2) {
                $dob = $data['month2'] . '/' . $data['day2'] . '/' . $data['year2'];
                $day = $data['day2'];
                $month = $data['month2'];
                $year = $data['year2'];
            }
        } else {
            $dob = $data['date_of_birth'];
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customer->setData('dob', $dob)->save();
        //$customer->setData('customer_age', $this->calculateAgeFromDOB($day, $month, $year))->save();
         }  catch (Exception $e){
            $error = $e->getMessage();
            $this->createErrorLog($customerId, $orderObj, $error);			
            Mage::getSingleton('core/session')->addError($error);
        }
	}
    public function createErrorLog($customerId, $orderObj, $error){
	     try{
			 $customer = Mage::getModel('customer/customer')->load($customerId);
			 $customerName = $customer->getFirstname()." ".$customer->getLastname(); 
			 $orderId = $orderObj->getId();
			 $logString = "Error Occurred in Updating the DOB for Order id = ".$orderId." Error =  ".$error." Order Was Updated By  ".$customerName." On ".Mage::getModel('core/date')->date('Y-m-d H:i:s');
			 $path = "./media/verifyUploads/dob/";
			 if (!file_exists($path)) {
					mkdir($path, 0777, true);
			 }
			 $log = fopen($path.$orderId."-dob-error.log", "a");
			 fwrite($log, "\n". $logString);
			 fclose($log);
		 }catch(Exception $e){		  
            Mage::getSingleton('core/session')->addError($e->getMessage());
		 }
    }

    /**
     * 
     * @param type $orderId
     * @return type int
     * check if uploading part is involved in Age Verification i.e. step 3 
     */
    public function checkMaxVerificationStep($order) {
        //$orderId = $order->getId();
        $quoteId = $order->getQuoteId();
        $quoteModel = Mage::getModel('sales/quote');
        //$checkoutMethod = $quoteModel->load($quoteId)->getCheckoutMethod();
        $item_quote = $quoteModel->getCollection()->addFieldToFilter('entity_id', $quoteId)->getFirstItem()->getData();
        $checkoutMethod = $item_quote['checkout_method'];
        $customerId = $order->getCustomerId();
        if ($checkoutMethod == 'register') {
            $varificationData = unserialize($item_quote['verification_data']);
            $apiDataArr = Mage::helper('ageverification')->getArrayColumn($varificationData, 'ageverification_step');
            if (array_search('3', $apiDataArr)) {
                $verificationStep = 3;
            }
        } else {
            $ageverificationModel = Mage::getModel('ageverification/ageverificationdetails');
            $verificationStep = $ageverificationModel->getCollection()->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('quote_id', $quoteId)->setOrder('date', DESC)->getFirstItem()->getAgeverificationStep();
        }
        if (!isset($verificationStep)) {
            $verificationStep = 0;
        }
        return $verificationStep;
    }

    /**
     * check if it exist in Rule
     * 0 stands for shipping state doesn't matches in age manager
     * 1 stands for shipping state matches in age manager and is not verified yet
     * 2 stands for shipping state matches in age manager and customer's curent age is greater than required minimum age for state.
     * 3 stands for shipping state matches in age manager and customer's curent age is less than required minimum age for state.
     */
    public function checkIfListedInRule($data = '') {
        if (empty($data)) {
            $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
            $country = $address->getCountryId();
            $regionId = $address->getRegionId();
            //get state code from region id
            $region = Mage::getModel('directory/region')->load($regionId);
            $state_code = $region->getCode();
        } else {
            $country = $data['country'];
            $state_code = $data['state'];
        }
        $collection = Mage::getModel('ageverification/ageverification')->getCollection()->addFieldToFilter('country', $country)->addFieldToFilter('state', array('*', $state_code));
        $isVerifiedForState = $this->isVerifiedForState($country, $state_code);
        if (!$collection->count() > 0) {
            return 0;
        } else if ($collection->count() > 0 && ($isVerifiedForState == 0)) {
            return 1;
        } else if ($collection->count() > 0 && ($isVerifiedForState == 1)) {
            return 2;
        } else if ($collection->count() > 0 && ($isVerifiedForState == 2)) {
            return 3;
        }
    }

    /**
     * 
     * @param type $data
     * method to save state for which customer is verified
     */
    public function saveVerifiedStateForCustomer($data, $custType = '') {
        $info = array();
        if ($custType == '') {

            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $info['customer_id'] = $customerId;
            //get state code from region id
            $region = Mage::getModel('directory/region')->load($data['regionId']);
            $state_code = $region->getCode();
            $info['country'] = $data['country'];
            $info['state'] = $state_code;
        } else {
            $info['customer_id'] = $data['customerId'];
            $info['country'] = $data['country'];
            $info['state'] = $data['state'];
        }
        $coll = Mage::getModel('ageverification/ageverificationforstate')->getCollection()->addFieldToFilter('country', $info['country'])->addFieldToFilter('state', $info['state']);
        if ($coll->count() == 0) {
            Mage::getModel('ageverification/ageverificationforstate')->setData($info)->save();
        }
    }

    /**
     * 
     * @param type $countryCode
     * @param type $state_code
     * @return int
     * check if a customer is verified for particular state 
     * 0 stands for not verified
     * 1 stands for age is verified and greater than minimum age requirement
     * 1 stands for age is verified and less than minimum age requirement
     */
    public function isVerifiedForState($countryCode, $state_code) {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if ($customerId && isset($countryCode) && !empty($countryCode) && isset($state_code) && !empty($state_code)) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $dobInfo = $customer->getData('dob');
            if (!isset($dobInfo) || empty($dobInfo)) {
                return 0;
            }
            $dobData = explode(' ', $dobInfo);
            $dobData = explode('-', $dobData[0]);
            $currentAge = $this->calculateAgeFromDOB($dobData[2], $dobData[1], $dobData[0]);
            $minimumAgeForState = Mage::getModel('ageverification/ageverification')->getCollection()->addFieldToFilter('country', $countryCode)->addFieldToFilter('state', $state_code)->getFirstItem()->getAge();
            if (isset($dobInfo) && !empty($dobInfo) && ($currentAge >= $minimumAgeForState)) {
                return 1;
            } else if (isset($dobInfo) && !empty($dobInfo) && ($currentAge < $minimumAgeForState)) {
                return 2;
            }
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param type $day
     * @param type $month
     * @param type $year
     * @return type
     * method to calculate todays age in years from date of birth 
     */
    public function calculateAgeFromDOB($day, $month, $year) {
        $from = new DateTime($year . '-' . $month . '-' . $day);
        $to = new DateTime('today');
        return $from->diff($to)->y;
    }

    public function getVerificationStatusOptions() {
        return array(
            'Age Verified' => $this->__('Age Verified'),
            'Age Verification Pending' => $this->__('Age Verification Pending')
        );
    }

    public function checkIfVirtualProduct() {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();
        foreach ($cartItems as $item) {
            if (!$item->getIsVirtual()) {
                return 0;
            }
        }
        return 1;
    }

    public function isTestModeEnabled() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('age_verify_general/ageverification_options/test', $storeId);
    }

    public function isLogEnabled() {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('age_verify_general/ageverification_options/api_curl_log', $storeId);
    }

}

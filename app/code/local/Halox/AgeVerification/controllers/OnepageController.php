<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';

class Halox_AgeVerification_OnepageController extends Mage_Checkout_OnepageController {

    /**
     * Save checkout billing address
     */
    public function saveBillingAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    //Used for age verification changes to be skipped
                    $result['verification_show'] = Mage::helper('ageverification')->checkIfListedInRule();
                    //end

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
                //Used for age verification changes to be skipped
                $result['verification_show'] = Mage::helper('ageverification')->checkIfListedInRule();
                //end
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * 
     * @return type
     * check in payment whether age verification step to be shown or not
     */
    public function savePaymentAction() {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);


            //check customer verified or not
            //$isCustomerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
            $isAgeVerifiedModuleEnable = Mage::helper('ageverification')->isAgeVerifiedEnable();
            $ifInRule = Mage::helper('ageverification')->checkIfListedInRule();
            $isVirtual = Mage::helper('ageverification')->checkIfVirtualProduct();

            if (empty($result['error'])) {
                if ($isAgeVerifiedModuleEnable && ($ifInRule == 1) && (!$isVirtual)) {
                    $result['goto_section'] = 'verify';
                    $result['update_section'] = array(
                        'name' => 'verify',
                        'html' => $this->_getVerifyHtml()
                    );
                } else {
                    $this->loadLayout('checkout_onepage_review');
                    $result['goto_section'] = 'review';
                    $result['update_section'] = array(
                        'name' => 'review',
                        'html' => $this->_getReviewHtml()
                    );
                }
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /*
     * Verification html on frontend checkout onepage get call from here
     */

    protected function _getVerifyHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_verify');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /*
     * All verification data for registered customers gets saved from from this method and for customers who choooses register method ,in that case data is saved in quote table.
     */

    public function saveVerifyAction() {

        if ($this->_expireAjax()) {
            return;
        }

        try {

            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            $result = array();

            $data = $this->getRequest()->getPost('verify', array());

            $result = $this->getOnepage()->saveVerify($data);
            
            //this line has been added because when age verification steps appears then all the reward points total dissappears
            $this->getOnepage()->getQuote()->collectTotals();

            if (!isset($result['error'])) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }

            $dob_details = $this->getRequest()->getPost();
            if (!empty($dob_details)) {
                $isVerified = Mage::helper('ageverification')->isCustomerDOBVerified();

                $data = $this->prepareData();
                if ($dob_details['verify']['status'] != 1) {
                    if ($customerId) {
                        if ($dob_details['verify']['step'] != 3) {
                            if (!$isVerified) {
                                $apiResponse = $this->saveCustomerVerificationData($data);
                            }

                            $result = isset($apiResponse) && (substr($this->getApiData($apiResponse), 0, 5) == 'ERROR') ? $this->getApiData($apiResponse) : $result;
                        } else {
                            if (!$isVerified) {
                                $this->saveCustomerVerificationData($data);
                            }
                        }
                    } else {
                        if ($dob_details['verify']['step'] != 3) {
                            $apiResponse = $this->saveCustomerVerificationDataInQuote($data);
                            $result = (substr($this->getApiData($apiResponse), 0, 5) == 'ERROR') ? $this->getApiData($apiResponse) : $result;
                        } else {
                            $this->saveCustomerVerificationDataInQuote($data);
                        }
                    }
                }
            }

            // get customer details and get api response ends
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Exception $e) {

            Mage::log($e, Zend_Log::ERR);

            $result['error'] = 1;
            $result['message'] = Mage::helper('checkout')->__('We are not able to verify your age at this time. Please contact customer support to complete your order.');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /*
     *  data is prepared from this method and passed to API
     */

    public function prepareData() {
        $storeId = Mage::app()->getStore()->getStoreId();
        $shippingAddr = Mage::helper('ageverification')->getShippingDetails();
        $shippingRegionId = Mage::helper('ageverification')->getShippingRegion();
        $dob_details = $this->getRequest()->getParams();

        $data = array_merge($shippingAddr, $dob_details['verify']);
        $data['user'] = Mage::getStoreConfig('age_verify_general/api_info/veratad_username', $storeId);
        $data['pass'] = Mage::getStoreConfig('age_verify_general/api_info/veratad_password', $storeId);

        $service = Mage::getStoreConfig('age_verify_general/api_info/veratad_service_name', $storeId);
        $url = Mage::getStoreConfig('age_verify_general/api_info/veratad_destination_url', $storeId);
        $data['service'] = $service;
        $data['url'] = $url;
        $data['age'] = Mage::helper('ageverification')->getAgeByregionId($shippingRegionId);
        return $data;
    }

    public function getApiData($apiResponse) {
        $response = $apiResponse; //$this->getApiResponse($data);
        $response = json_decode($response);
        $errorDetail = isset($response->error->detail) ? $response->error->detail : $response->result->detail;
        $response = isset($response->result->action) ? ($response->result->action) : ($response->error->message);

        if ($response != 'PASS' || empty($response) || $response == 'FAIL') {
            return 'ERROR' . '---' . $response . '---' . $errorDetail;
        } else if ($response == 'PASS') {
            $isPassedFromAPI = Mage::helper('ageverification')->isPassedFromAPI();
            if ($isPassedFromAPI) {
                $params = $this->getRequest()->getParams();
                Mage::helper('ageverification')->setCustomerDOB($params['verify']);
            }
        }
    }

    /*
     * customer verification data for registered customer is saved in custom table from this method.
     */

    public function saveCustomerVerificationData($data) {
        $info = array();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
        $info['customer_id'] = $customerId;
        $ageverificationModel = Mage::getModel('ageverification/ageverificationdetails');

        //response and confirmation and error detail from api
        $rawResponse = $this->getApiResponse($data);
        $response = json_decode($rawResponse);
        $errorDetail = isset($response->error->detail) ? $response->error->detail : $response->result->detail;
        $confirmation = isset($response->result->action) ? $response->meta->confirmation . ',' . $response->result->action : $response->result->action . ',FAIL';

        $info['ageverification_step'] = $data['step'];
        $info['service'] = $data['service'];
        $info['confirmation'] = ($data['step'] == 3) ? 'REVIEW' : $confirmation;
        $info['first_name'] = $data['firstname'];
        $info['last_name'] = $data['lastname'];
        $info['address'] = $data['address'][0] . ' ' . $data['address'][1];
        $info['city'] = $data['city'];
        $info['state'] = $data['region'];
        $info['country'] = Mage::app()->getLocale()->getCountryTranslation($data['country']);
        $info['ssn'] = ($data['step'] == 2) ? substr($data['ssn'], -4) : '';
        $info['zipcode'] = $data['postcode'];
        $info['date_of_birth'] = ($data['step'] == 2) ? $data['month2'] . '/' . $data['day2'] . '/' . $data['year2'] : $data['month'] . '/' . $data['day'] . '/' . $data['year'];
        $info['fail_message'] = ($data['step'] == 3) ? '' : $errorDetail;
        $info['api_response'] = ($data['step'] == 3) ? '' : $rawResponse;
        $info['quote_id'] = $quote_id;

        $ageverificationModel->setData($info)->save();

        return $rawResponse;
    }

    /*
     * customer verification data for unregistered customer is saved in quote table from this method.
     */

    public function saveCustomerVerificationDataInQuote($data) {
        $info = array();
        $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();

        //response and confirmation from api
        $rawResponse = $this->getApiResponse($data);
        $response = json_decode($rawResponse);
        $errorDetail = isset($response->error->detail) ? $response->error->detail : $response->result->detail;
        $confirmation = isset($response->result->action) ? $response->meta->confirmation . ',' . $response->result->action : $response->result->action . ',FAIL';

        $info['ageverification_step'] = $data['step'];
        $info['service'] = $data['service'];
        $info['confirmation'] = ($data['step'] == 3) ? 'REVIEW' : $confirmation;
        $info['first_name'] = $data['firstname'];
        $info['last_name'] = $data['lastname'];
        $info['address'] = $data['address'][0] . ' ' . $data['address'][1];
        $info['city'] = $data['city'];
        $info['state'] = $data['region'];
        $info['country'] = Mage::app()->getLocale()->getCountryTranslation($data['country']);
        $info['ssn'] = ($data['step'] == 2) ? substr($data['ssn'], -4) : '';
        $info['zipcode'] = $data['postcode'];
        $info['date_of_birth'] = ($data['step'] == 2) ? $data['month2'] . '/' . $data['day2'] . '/' . $data['year2'] : $data['month'] . '/' . $data['day'] . '/' . $data['year'];
        $info['fail_message'] = ($data['step'] == 3) ? '' : $errorDetail;
        $info['api_response'] = ($data['step'] == 3) ? '' : $rawResponse;
        $info['quote_id'] = $quote_id;

        $quoteModel = Mage::getModel('sales/quote');
        if ($data['step'] == 1) {
            $verifyData = array();
            $verifyData[] = $info;
        } else {
            $verifyData = unserialize($quoteModel->load($quote_id)->getVerificationData());
            $verifyData[] = $info;
        }
        $quoteModel->setData('verification_data', serialize($verifyData))->setId($quote_id)->save();

        return $rawResponse;
    }

    /*
     * API response is get from this method after passing input data
     */

    public function getApiResponse($inputData) {
        $storeId = Mage::app()->getStore()->getStoreId(); // ID of the store you want to fetch the value of
        $isTestModeEnabled = Mage::helper('ageverification')->isTestModeEnabled();
        $isLogEnabled = Mage::helper('ageverification')->isLogEnabled();

        $apiInputArray = array(
            'user' => $inputData['user'],
            'pass' => $inputData['pass'],
            'service' => $inputData['service'],
            'reference' => '123456',
            'target' => array(
                'fn' => $inputData['firstname'],
                'ln' => $inputData['lastname'],
                'addr' => $inputData['address'][0] . ' ' . $inputData['address'][1],
                'city' => $inputData['city'],
                'state' => $inputData['region'],
                'zip' => $inputData['postcode'],
                'dob' => ($inputData['step'] == 2) ? $inputData['year2'] . (strlen($inputData['month2']) == 2 ? $inputData['month2'] : '0' . $inputData['month2']) . $inputData['day2'] : $inputData['year'] . (strlen($inputData['month']) == 2 ? $inputData['month'] : '0' . $inputData['month']) . $inputData['day'],
                'age' => $inputData['age'],
                'dob_type' => 'YYYYMMDD',
                'ssn' => ($inputData['step'] == 1 || $inputData['step'] == 3) ? '' : $inputData['ssn']
            ),
        );

        if ($isTestModeEnabled) {
            $keyArr = array('test_key' => 'general');
            $apiTargetArr = array_merge($apiInputArray['target'], $keyArr);
            $apiInputArray['target'] = $apiTargetArr;
        }

        $data = json_encode($apiInputArray);

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        $curlInputdata = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $inputData['url'],
            //CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE => 1,
            CURLOPT_TIMEOUT => 10
        );

        if ($isLogEnabled) {
            $logPath = Mage::getBaseDir() . DS
                    . 'var' . DS . 'log'
                    . DS . 'curl_ageverification.log';
            $curlLog = fopen($logPath, 'a+');
            $curlInputdata[CURLOPT_STDERR] = $curlLog;
        }
         curl_setopt_array($curl, $curlInputdata);
        // Send the request & save response to $resp
        if (!$resp = curl_exec($curl)) {
            Mage::throwException('Request to Veritard API failed.');
        }
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }

    /*
     * Uploaded document on verification step is saved from this method.
     */

    public function uploadVerifyAction() {

        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {

            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('filename');

                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                $fileName = uniqid($customerId . '-') . '.' . $extension;
                // Any extention would work

                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png'));
                $uploader->setAllowCreateFolders(true);
                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'verifyUploads';

                $uploader->save($path, $fileName);

                //this way the name is saved in DB
                $data['verify_document'] = $fileName;
                if ($customerId) {
                    $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
                    $model = Mage::getModel('ageverification/ageverificationdetails');
                    $verifyId = $model->getCollection()->addFieldToFilter('customer_id', $customerId)
                            ->addFieldToFilter('quote_id', $quote_id)
                            ->getLastItem()
                            ->getId();
                    $model->setData($data)->setId($verifyId)->save();
                } else {
                    $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
                    $quoteModel = Mage::getModel('sales/quote');
                    $verifyData = unserialize($quoteModel->load($quote_id)->getVerificationData());
                    $verificationStepsArr = Mage::helper('ageverification')->getArrayColumn($verifyData, 'ageverification_step');
                    $key = array_search('3', $verificationStepsArr);
                    if ($key) {
                        $verifyData[$key]['verify_document'] = $fileName;
                        $quoteModel->setData('verification_data', serialize($verifyData))->setId($quote_id)->save();
                    }
                }
            } catch (Exception $e) {
                Mage::log($e, Zend_Log::ERR);
                $result['error'] = 1;
                $result['message'] = $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            // $result will contain error data if shipping method is empty
            if (!$result) {
                Mage::dispatchEvent(
                        'checkout_controller_onepage_save_shipping_method', array(
                    'request' => $this->getRequest(),
                    'quote' => $this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }

            /**
             * FIX: shipping fees was not getting updated correctly based
             * on the chosen shipping method so setting total_collected_flag to 
             * false to force Magento calculated it once again.
             */
            $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);

            $this->getOnepage()->getQuote()->collectTotals()->save();

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

}

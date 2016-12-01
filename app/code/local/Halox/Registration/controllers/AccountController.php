<?php

class Halox_Registration_AccountController extends Mage_Core_Controller_Front_Action {

    const CUSTOMER_ID_SESSION_NAME = "customerId";
    const TOKEN_SESSION_NAME = "token";

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = strtolower($this->getRequest()->getActionName());
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
            'success'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch() {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    /**
     * Default customer account page
     */
    public function indexAction() {

        $this->_redirect('customer/account');
        /*$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
                $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();*/
    }

    /**
     * Customer register form page
     */
    public function createAction() {

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Create customer account action return $this->getResponse()->setBody('true');
     */
    public function createPostAction() {

        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
        $customer_id = $this->getRequest()->getPost('customer_id');

        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirectError($errUrl);
            return;
        }

        if ($this->getRequest()->getPost('customer_id')) {
            $customer = $this->_getModel('customer/customer')->load($customer_id);
        } else {
            $customer = $this->_getCustomer();
        }

        try {
            $errors = $this->_getCustomerErrors($customer);
            if (empty($errors)) {
                    $customer->cleanPasswordsValidationData();
                    if($this->getRequest()->getPost('password_hash')!='')
                    {
                    $customer->setPasswordHash($this->getRequest()->getPost('password_hash'));
                   }
                    $customer->save();
                /* save telephone no in db */
                if ($this->getRequest()->getPost('first_telephone')) {
                    /* to save data in one customer address object.
                     * if customer go back after filling 1 step form.
                     */
                    $address_id = $customer->getDefaultBilling();
                    if ($address_id != "") {
                        Mage::getModel('customer/address')->load($address_id)->delete();
                    }
                    /* close */
                    $addressData = array('firstname' => $customer->getFirstname(), 'lastname' => $customer->getLastname(), 'telephone' => $this->getRequest()->getPost('first_telephone'), 'is_default_billing' => 1, 'is_default_shipping' => 1);
                    $customerAddress = Mage::getModel('customer/address');
                    $customerAddress->setData($addressData)
                            ->setCustomerId($customer->getId());
                    $customerAddress->save();
                }
                /* close */
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {

                return $this->getResponse()->setBody(json_encode($errors));
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('email exists');
            } else {
                $message = $this->_escapeHtml($e->getMessage());
            }

            return $this->getResponse()->setBody(json_encode($message));
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            $session->addException($e, $this->__('Cannot save the customer.'));
        }

        return $this->getResponse()->setBody(json_encode($errors));
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer) {

        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store */
            $store = $app->getStore();
            $customer->sendNewAccountEmail(
                    'confirmation', $session->getBeforeAuthUrl(), $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } 
        if ($this->getRequest()->getPost('addi_info')) {
            $url = $this->_welcomeCustomer($customer);
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            return $this;
        } else {
            $customerData = Mage::getModel('customer/customer')->load($customer->getId());
            $address_id = $customerData->getDefaultBilling();
            $address = Mage::getModel('customer/address')->load($address_id);
            return $this->getResponse()->setBody(json_encode(array('cust_id' => $customer->getId(), 'first_name' => $customer->getFirstname(), 'last_name' => $customer->getLastname(), 'telephone_no' => $address->getTelephone(),'password_hash'=>$customerData['password_hash'], 'result' => true)));
        }
    }

    /**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer() {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }

    /**
     * Add session error method
     *
     * @param string|array $errors
     */
    protected function _addSessionError($errors) {
        $session = $this->_getSession();
        $session->setCustomerFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($this->_escapeHtml($errorMessage));
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }

    /**
     * Escape message text HTML.
     *
     * @param string $text
     * @return string
     */
    protected function _escapeHtml($text) {
        return Mage::helper('core')->escapeHtml($text);
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer) {

        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {


            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);

        if ($customerErrors !== true) {

            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();

            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }

        return $errors;
    }

    /**
     * Get Customer Form Initalized Model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm($customer) {
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = $this->_getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);
        return $customerForm;
    }

    /**
     * Get Helper
     *
     * @param string $path
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($path) {
        return Mage::helper($path);
    }

    /**
     * Get App
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp() {
        return Mage::app();
    }

    /**
     * Dispatch Event
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _dispatchRegisterSuccess($customer) {
        Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
        );
    }

    /**
     * Gets customer address
     *
     * @param $customer
     * @return array $errors
     */
    protected function _getErrorsOnCustomerAddress($customer) {
        $errors = array();
        /* @var $address Mage_Customer_Model_Address */
        $address_id = $customer->getDefaultBilling();
        $addressData = Mage::getModel('customer/address')->load($address_id);
        $customer->setTelephone($addressData->getTelephone());
        if ($address_id != "") {
            Mage::getModel('customer/address')->load($address_id)->delete();
        }

        $address = Mage::getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = $this->_getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
                ->setEntity($address);

        $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
        $addressErrors = $addressForm->validateData($addressData);
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        $address->setId(null)
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        $addressForm->compactData($addressData);
        $customer->addAddress($address);

        $addressErrors = $address->validate();
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }

        return $errors;
    }

    /**
     * Get model by path
     *
     * @param string $path
     * @param array|null $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function _getModel($path, $arguments = array()) {
        return Mage::getModel($path, $arguments);
    }

    /**
     * Get model from registry by path
     *
     * @param string $path
     * @return mixed
     */
    protected function _getFromRegistry($path) {
        return Mage::registry($path);
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getSession()->addSuccess(
                //$this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
                $this->__('A Nicopure Labs team member will be reviewing your application. If any additional information is needed to process your application, one of our dedicated account managers will contact you within 48 hours. Once your wholesale application is approved, you will receive a follow up email outlining the next steps.')
        );
        $customMsg = Mage::getStoreConfig('amcustomerattr/activation/registration_notice', Mage::app()->getStore()->getId());
        if ($customMsg) {
            $this->_getSession()->addNotice($customMsg);
        }
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = $this->_getHelper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', $this->_getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', $this->_getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );

        $successUrl = $this->_getUrl('registration/account/success/', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getUrl('registration/account/success/', array('_secure' => true));
        }

        return $successUrl;
    }

    /**
     * Get Url method
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = array()) {
        return Mage::getUrl($url, $params);
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null) {
        return $this->_getHelper('customer/address')->isVatValidationEnabled($store);
    }

    /**
     * Customer logout action
     */
    public function logoutAction() {

        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_getSession()->addSuccess(
                $this->__('You have logged out.')
        );
        $this->_redirect('customer/account/login');
    }

    /**
     * Login post action
     */
    public function loginPostAction() {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }

    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect() {

        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl()/* || $session->getBeforeAuthUrl() == Mage::getBaseUrl()*/) {
            // Set default URL to redirect customer to
            //$session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl( $this->_getHelper('core')->urlDecodeAndEscape($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
            //$session->setBeforeAuthUrl( $this->_getHelper('customer')->getDashboardUrl());
            $session->setBeforeAuthUrl( Mage::getBaseUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }

    public function successAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

}

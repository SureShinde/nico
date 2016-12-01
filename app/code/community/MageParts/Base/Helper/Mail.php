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
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Mail extends MageParts_Base_Helper_Data
{

    /**
     * Send off an email
     *
     * The data parameter is an array accepting the following keys:
     *
     *   - customer (Mage_Customer_Model_Customer)
     *   - email (string, overrides customer->getEmail())
     *   - name (string, overrides $customer->getName())
     *   - templateData (array, data to be submitted to the email template)
     *   - store_id (int, overrides normal store id calculation behaviour)
     *
     * @param array $customerData
     * @param string $template
     * @param string $identity
     * @param array $templateData
     * @param int $storeId
     * @return MageParts_Base_Helper_Mail
     * @throws Exception
     */
    public function send($customerData=array(), $template, $identity, $templateData=array(), $storeId=null)
    {
        $translate = Mage::getSingleton('core/translate');

        $sid = !is_null($storeId) ? $storeId : $this->getStoreId();

        /* @var Mage_Customer_Model_Customer $customer */
        $customer = isset($customerData['customer']) ? $customerData['customer'] : null;

        if (!is_null($customer) && ($customer instanceof Mage_Customer_Model_Customer) && $customer->getId()) {
            if (!isset($customerData['email']) || !$customerData['email']) {
                $customerData['email'] = $customer->getEmail();
            }

            if (!isset($customerData['name']) || !$customerData['name']) {
                $customerData['name'] = $customer->getName();
            }

            if (is_null($storeId)) {
                $sid = $this->getSendMailStoreId($customer);
            }
        }

        $sid = !is_array($sid) ? array($sid) : $sid;

        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        // let's submit the email
        if (isset($customerData['email']) && $customerData['email']) {
            foreach ($sid as $s) {
                $templateModel = Mage::getModel('core/email_template');

                $templateModel->setDesignConfig(array('area' => 'frontend', 'store' => $s))
                    ->sendTransactional(
                        $template,
                        $identity,
                        $customerData['email'],
                        isset($customerData['name']) ? $customerData['name'] : '',
                        $templateData
                    );

                if (!$templateModel->getSentSuccess()) {
                    throw new Exception(Mage::helper('mageparts_base')->__('The email could not be sent.'));
                }
            }
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Get customer "send mail store id" from customer object instance.
     *
     * @throws Exception
     * @param int|Mage_Customer_Model_Customer $customer
     * @return int
     */
    public function getSendMailStoreId($customer)
    {
        $result = 0;

        if (!is_object($customer) && (int) $customer > 0) {
            $customer = Mage::getModel('customer/customer')->load($customer);
        }

        if (!($customer instanceof Mage_Customer_Model_Customer)) {
            throw new Exception("Unexpected instance type received, expected Mage_Customer_Model_Customer");
        }

        if (version_compare(Mage::getVersion(), '1.5.0.0', '<')) {
            if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
                $result = $customer->getSendemailStoreId() > 0 ? $customer->getSendemailStoreId() : $result;
            }
        } else {
            $result = $customer->getSendemailStoreId();
        }

        if (!((bool) $result) && $customer->getWebsiteId() > 0) {
            $sids = Mage::app()->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($sids);
            $result = current($sids);
        }

        return $result;
    }

    /**
     * Send one and the same email to several people at once.
     *
     * @param array|string $recipients If this is a string it will assume a config path, arrays may be handled in various way please check the code of the function for a better reference.
     * @param string $template
     * @param string $identity
     * @param array $data
     * @param array $templateData
     * @param null|int $storeId
     */
    public function walk($recipients, $template, $identity, $data=array(), $templateData=array(), $storeId=null)
    {
        if (is_string($recipients)) {
            // get recipient list from config
            $conf = Mage::getStoreConfig($recipients);

            if (!empty($conf)) {
                // check if config value is serialized
                if ($this->isSerialized($conf)) {
                    $recipients = unserialize($conf);
                } else {
                    if (strrpos($conf, ',') > 0) {
                        $arr = explode(',', $conf);

                        if (count($arr)) {
                            $recipients = $arr;
                        }
                    }
                    else if (strrpos($conf,'@') > 0) {
                        $recipients = array($conf);
                    }
                }
            }
        }

        if (is_array($recipients) && count($recipients)) {
            foreach ($recipients as $r) {
                $data['email'] = $r;
                $this->send($data, $template, $identity, $templateData, $storeId);
            }
        }
    }

    /**
     * Check if debugger is enabled or not for content mail functions.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return parent::isDebugEnabled() && Mage::getStoreConfigFlag('mageparts_base/debug/mail');
    }

}

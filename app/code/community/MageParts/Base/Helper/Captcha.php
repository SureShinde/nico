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

class MageParts_Base_Helper_Captcha extends MageParts_Base_Helper_Data
{

    /**
     * Validate Zend Captcha.
     *
     * @param array $data
     * @return array
     */
    public function validate(array $data)
    {
        $result = array(
            'valid' => false,
            'error' => ''
        );

        if (!isset($data[Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE])) {
            $data[Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE] = Mage::app()->getRequest()->getParam(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        }

        if (is_array($data) && isset($data['captcha_form_id'])) {
            $model = Mage::helper('captcha')->getCaptcha($data['captcha_form_id']);

            if ($model) {
                if ($model->isRequired()) {
                    $params = $data[Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE];

                    if (is_array($params) && isset($params[$data['captcha_form_id']])) {
                        $answer = $params[$data['captcha_form_id']];

                        if ($model->isCorrect($answer)) {
                            $result['valid'] = true;
                        } else {
                            $result['error'] = $this->__(Mage::getStoreConfig('mageparts_base/captcha/err_wrong_code'));
                        }
                    }
                } else {
                    $result['valid'] = true;
                }
            }
        }

        if (!$result['valid'] && empty($result['error'])) {
            $result['error'] = $this->__(Mage::getStoreConfig('mageparts_base/captcha/err_default'));
        }

        return $result;
    }

    /**
     * Check if captcha is enabled.
     *
     * @param string $id
     * @return boolean
     */
    public function isCaptchaEnabled($id)
    {
        $result = false;

        if (!empty($id)) {
            $config = Mage::getStoreConfig('customer/captcha/forms');

            if (!empty($config)) {
                $config = explode(',', $config);
                $result = (is_array($config) && in_array($id, $config));
            }
        }

        return $result;
    }

}

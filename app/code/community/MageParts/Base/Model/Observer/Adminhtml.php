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

class MageParts_Base_Model_Observer_Adminhtml
{

    /**
     * Name of controller for current request.
     *
     * @var string
     */
    protected $_controllerName;

    /**
     * Name of action for current request.
     *
     * @var string
     */
    protected $_actionName;

    /**
     * After applying changes to the configuration we should reload all cached data
     * associated with the extension, and perhaps also perform some other actions.
     *
     * @param Varien_Event_Observer $observer
     */
    public function changedCfg(Varien_Event_Observer $observer)
    {
        // reload all cache data related to the extension
        Mage::helper('mageparts_base/cache')->clean('all');
    }

    /**
     * Clear out data set as "Use Config Settings" before saving a model.
     *
     * Please see MageParts_Base_Block_Adminhtml_Configdefaults :: FIELD_USE_CONFIG_SETTINGS_NAMESPACE
     *
     * @param Varien_Event_Observer $observer
     */
    public function clearConfigDefaultValues(Varien_Event_Observer $observer)
    {
        /* @var $model Mage_Core_Model_Abstract */
        $model = is_object($observer) ? $observer->getObject() : null;

        if (is_object($model)) {
            $data = Mage::app()->getRequest()->getParam(MageParts_Base_Block_Adminhtml_Configdefaults::FIELD_USE_CONFIG_SETTINGS_NAMESPACE);

            if (is_array($data)) {
                foreach (array_keys($data) as $attribute) {
                    $model->setData($attribute, null);
                }
            }
        }
    }

    /**
     * Retrieve controller name of current request.
     *
     * @return string
     */
    public function _getControllerName()
    {
        if (is_null($this->_controllerName)) {
            $this->_controllerName = Mage::app()->getRequest()->getControllerName();
        }

        return $this->_controllerName;
    }

    /**
     * Retrieve action name of current request.
     *
     * @return string
     */
    public function _getActionName()
    {
        if (is_null($this->_actionName)) {
            $this->_actionName = Mage::app()->getRequest()->getActionName();
        }

        return $this->_actionName;
    }

    /**
     * Retrieve helper object.
     *
     * @return MageParts_Base_Helper_Data
     */
    public function _getHelper()
    {
        return Mage::helper('mageparts_base');
    }

}

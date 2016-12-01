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

class MageParts_Base_Block_Adminhtml_Configdefaults extends Mage_Core_Block_Template
{

    /**
     * Model key that defines what attributes & properties have applied configuration default data.
     */
    const MODEL_KEY_FIELD_HAS_DEFAULT_DATA = 'mp_field_has_default_data';

    /**
     * Namespace for "Une Config Settings" checkboxes, used when model is being saved to determine whether data should
     * remain or be cleared out. Otherwise, if you first de-select "Use Config Settings", fill out a value, and then try
     * to revert the process the data will remain since the field is actually disabled, thus never posted and so it's
     * ignored. Please see MageParts_Base_Model_Observer_Adminhtml :: clearConfigDefaultValues()
     */
    const FIELD_USE_CONFIG_SETTINGS_NAMESPACE = 'mp_use_config';

    /**
     * This should be the registry key associated with the model from which we will check what attributes & properties
     * are using the default configuration based values. This information is collected and stored directory on the model
     * itself in MageParts_Base_Model_Observer_Adminhtml :: appendConfigurationDefaultsToModelData()
     *
     * @var string
     */
    protected $_modelRegistryKey;

    /**
     * Consider the following structure for a field name (<input name=): product[mp_pp_restricted]
     *
     * In the example above "product" is the namespace.
     *
     * @var string
     */
    protected $_fieldNamespace;

    /**
     * Active model, model currently being edited in administration panel.
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_activeModel;

    /**
     * Retrieve model registry key name.
     *
     * @param string $key
     * @return MageParts_Base_Block_Adminhtml_Configdefaults
     */
    public function setModelRegistryKey($key)
    {
        $this->_modelRegistryKey = $key;
    }

    /**
     * Set field namespace.
     *
     * @param string $key
     * @return MageParts_Base_Block_Adminhtml_Configdefaults
     */
    public function setFieldNamespace($key)
    {
        $this->_fieldNamespace = $key;
    }

    /**
     * Retrieve field namespace.
     *
     * @return string
     */
    public function getFieldNamespace()
    {
        return $this->_fieldNamespace;
    }

    /**
     * Retrieve model registry key.
     *
     * @return string
     */
    public function getModelRegistryKey()
    {
        return $this->_modelRegistryKey;
    }

    /**
     * Get field list.
     *
     * @return JSON
     */
    public function getFieldList()
    {
        return json_encode($this->_getHelper()->getConfigurationDefaults($this->getActiveModel(), array(), true));
    }

    /**
     * Get list of fields which currently uses default config data.
     *
     * @return JSON
     */
    public function getActiveFieldList()
    {
        $result = array();
        $model = $this->getActiveModel();

        if ($model) {
            $data = $model->getData(self::MODEL_KEY_FIELD_HAS_DEFAULT_DATA);

            if (is_array($data)) {
                $result = $data;
            }
        }

        return json_encode($result);
    }

    /**
     * Retrieve active model (by using $this->getModelRegistryKey()).
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getActiveModel()
    {
        if (is_null($this->_activeModel)) {
            $result = false;

            if ($this->getModelRegistryKey()) {
                $model = Mage::registry($this->getModelRegistryKey());

                if (is_object($model)) {
                    $result = $model;
                }
            }

            $this->_activeModel = $result;
        }

        return $this->_activeModel;
    }

    /**
     * This block can only be rendered as long as we are in the administration panel adn have selected the default store
     * view. In all other cases where lower store view levels can be used (for example when editing products or
     * categories) the "Use Config Settings" will be replaced by the "Use Default Value" instead.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $result = '';

        if ($this->_getHelper()->isAdmin() && $this->_getHelper()->getStoreId() === 0) {
            $result = parent::_toHtml();
        }

        return $result;
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

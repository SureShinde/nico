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

class MageParts_Base_Model_Observer_Global
{

    /**
     * Append configuration defaulted data to eav based collections.
     *
     * About default values. Please keep in mind that default values are the values from store view 0, ie. in the admin
     * panel, if you choose a value for an attribute, and then a separate value for a specific store view, then the
     * default value will be the one set on store view 0. So default in this function _never_ refers to the attribute
     * property default_value (in the table eav_attribute). It always refers to the attribute value, set on store view 0.
     *
     * @param Varien_Event_Observer $observer
     */
    public function appendConfigurationDefaultsToCollection(Varien_Event_Observer $observer)
    {
        /* @var $collection Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Resource_Db_Collection_Abstract */
        $collection = is_object($observer) ? $observer->getCollection() : null;

        if (is_object($collection)) {
            $modelName = $this->_getHelper()->getModelNameFromCollection($collection);

            if ($modelName) {
                $map = $this->_getHelper()->getConfigurationDefaults($modelName);

                if (count($map)) {
                    // whether or not the collection is EAV
                    $isEav = $this->_getHelper()->isEavObject($collection);

                    // "columns" (SELECT) section of SQL query
                    $columnCollection = $collection->getSelect()->getPart(Zend_Db_Select::COLUMNS);

                    // "where" section of SQL query
                    $whereCollection = $collection->getSelect()->getPart(Zend_Db_Select::WHERE);

                    // "from" section of SQL query
                    $fromCollection = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

                    foreach ($map as $attribute => $path) {
                        $configValue = addslashes(Mage::getStoreConfig($path));

                        if (!is_null($configValue)) {
                            // Whether or not a column definition for this attribute was found (if not we will add one)
                            $foundColumnDefinition = false;

                            // Correct columns
                            if (count($columnCollection)) {
                                foreach ($columnCollection as $key => $column) {
                                    if ($isEav) {
                                        if (isset($column[1]) && $column[1] instanceof Zend_Db_Expr && strpos((string)$column[1], ('at_' . $attribute . '_default')) !== FALSE) {
                                            // Read more about default values and what they are in the function comment above.
                                            $columnCollection[$key][1] = new Zend_Db_Expr(preg_replace('/(at_' . $attribute . '_default\.value)/', ("IFNULL($1, '" . $configValue . "')"), $column[1]->__toString()));
                                            $foundColumnDefinition = true;
                                        }
                                    } else {
                                        if ($column[1] === $attribute) {
                                            $columnCollection[$key] = array(
                                                'main_table',
                                                new Zend_Db_Expr("IFNULL(" . $attribute . ", '" . $configValue . "')"),
                                                $attribute
                                            );

                                            $foundColumnDefinition = true;
                                        }
                                    }
                                }
                            }

                            // Correct where statements
                            if (count($whereCollection)) {
                                foreach ($whereCollection as $key => $where) {
                                    if ($isEav) {
                                        // Read more about default values and what they are in the function comment above.
                                        if (strpos($where, ('at_' . $attribute . '_default')) !== FALSE) {
                                            $whereCollection[$key] = preg_replace('/(at_' . $attribute . '_default\.value)/', ("IFNULL($1, '" . $configValue . "')"), $where);
                                        }
                                    } else {
                                        if (strpos($where, $attribute) !== FALSE) {
                                            $whereCollection[$key] = preg_replace('/(' . $attribute . ')/', ("IFNULL($1, '" . $configValue . "')"), $where);
                                        }
                                    }
                                }
                            }

                            /* This means that there is no column definition in the SELECT part of the SQL query for
                                   this attribute. Without one, our configuration default values will be ignored, so we
                                   add a "column" reference ourselves if it doesn't exist. */
                            if (!$foundColumnDefinition) {
                                if ($isEav) {
                                    $columnCollection[] = array(
                                        'at_' . $attribute,
                                        new Zend_Db_Expr("IF(at_" . $attribute . ".value_id > 0, at_" . $attribute . ".value, IFNULL(at_" . $attribute . "_default.value, '" . $configValue . "'))"),
                                        $attribute
                                    );

                                    $attributeData = $this->_getHelper()->getAttributeData($collection->getEntity()->getTypeId(), $attribute, array('attribute_id', 'backend_type'));

                                    // Read more about default values and what they are in the function comment above.
                                    if (is_array($attributeData) && isset($attributeData['backend_type']) && isset($attributeData['attribute_id'])) {
                                        $fromCollection['at_' . $attribute . '_default'] = array(
                                            'joinType' => 'left join',
                                            'schema' => null,
                                            'tableName' => $collection->getEntity()->getEntityTable() . '_' . $attributeData['backend_type'],
                                            'joinCondition' => "(`at_" . $attribute . "_default`.`entity_id` = `e`.`entity_id`) AND (`at_" . $attribute . "_default`.`attribute_id` = '" . $attributeData['attribute_id'] . "') AND (`at_" . $attribute . "_default`.`store_id` = 0)"
                                        );

                                        $fromCollection['at_' . $attribute] = array(
                                            'joinType' => 'left join',
                                            'schema' => null,
                                            'tableName' => $collection->getEntity()->getEntityTable() . '_' . $attributeData['backend_type'],
                                            'joinCondition' => "(`at_" . $attribute . "`.`entity_id` = `e`.`entity_id`) AND (`at_" . $attribute . "`.`attribute_id` = '" . $attributeData['attribute_id'] . "') AND (`at_" . $attribute . "`.`store_id` = " . $this->_getHelper()->getStoreId() . ")"
                                        );
                                    }
                                } else {
                                    $columnCollection[] = array(
                                        'main_table',
                                        new Zend_Db_Expr("IFNULL(" . $attribute . ", '" . $configValue . "')"),
                                        $attribute
                                    );
                                }
                            }
                        }
                    }

                    $collection->getSelect()
                        ->setPart(Zend_Db_Select::COLUMNS, $columnCollection)
                        ->setPart(Zend_Db_Select::WHERE, $whereCollection)
                        ->setPart(Zend_Db_Select::FROM, $fromCollection);
                }
            }
        }
    }

    /**
     * Append configuration defaulted data to models.
     *
     * @param Varien_Event_Observer $observer
     */
    public function appendConfigurationDefaultsToModel(Varien_Event_Observer $observer)
    {
        /* @var $model Mage_Core_Model_Abstract */
        $model = is_object($observer) ? $observer->getDataObject() : null;

        if (is_object($model) && $model->getResourceName()) {
            $map = $this->_getHelper()->getConfigurationDefaults($model->getResourceName());

            if (count($map)) {
                $defaultData = array();

                foreach ($map as $attribute => $path) {
                    if (is_null($model->getData($attribute))) {
                        $value = Mage::getStoreConfig($path);

                        if (!is_null($value)) {
                            $model->setData($attribute, $value);
                            $defaultData[] = $attribute;
                        }
                    }
                }

                if ($this->_getHelper()->isAdmin()) {
                    $model->setData(MageParts_Base_Block_Adminhtml_Configdefaults::MODEL_KEY_FIELD_HAS_DEFAULT_DATA, $defaultData);
                }
            }
        }
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

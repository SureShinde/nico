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

class MageParts_Base_Helper_Cache extends MageParts_Base_Helper_Data
{

    /**
     * Tags which are included on all caches.
     *
     * @var array
     */
    protected $_globalTags = array('mageparts_base');

    /**
     * Save cache data
     *
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @param   int $lifetime (in seconds)
     * @param   boolean $includeLeechTags (whether or not to include global tags from leeches)
     * @param   boolean $register (whether or not to store data in registry as well)
     * @return  MageParts_Base_Helper_Cache
     */
    public function save($data, $id, $tags = array(), $lifetime = null, $includeLeechTags = false, $register = false)
    {
        try {
            if ($this->isCacheEnabled()) {
                // attempt to load configured lifetime from module config
                if (is_null($lifetime)) {
                    $lifetime = $this->_getConfigLifetime();
                }

                // debug log
                if (is_string($data)) {
                    $this->log("Saving cache with id '" . $id . "' data length " . strlen($data));
                } else {
                    $this->log("Saving cache with id '" . $id . "' carrying object.");
                }

                // collect cache tags
                $tags = array_merge($tags, $this->_globalTags);

                if ($includeLeechTags) {
                    $leeches = $this->getModuleLeeches();

                    if (count($leeches)) {
                        foreach ($leeches as $leech) {
                            $leechHelper = $this->getLeechHelper($leech);

                            if ($leechHelper) {
                                $tags = array_merge($tags, $leechHelper->getGlobalTags());
                            }
                        }
                    }
                }

                // more logging
                $this->log('Cache tags:');
                $this->log($tags);

                $lifetime = (int) $lifetime < 1 ? null : $lifetime;

                Mage::getModel('core/cache')->save($data, $id, array_unique($tags), $lifetime);
            }

            if ($register) {
                Mage::register($id, $data);
            }
        } catch (Exception $e) {
            $this->log($e);
        }

        return $this;
    }

    /**
     * Load cache data
     *
     * @param string $id
     * @param boolean $register (whether or not to load data from registry if nothing is found in the cache).
     * @return mixed
     */
    public function load($id, $register=false)
    {
        $result = null;

        if ($this->isCacheEnabled()) {
            // debug log
            $this->log('Loading cache with id ' . $id);

            // load data from cache
            $result = Mage::getModel('core/cache')->load($id);
        }

        if (is_null($result) && $register) {
            $result = Mage::registry($id);
        }

        return $result;
    }

    /**
     * Check if extension caching is enabled.
     *
     * @param string $type
     * @return bool
     */
    public function isCacheEnabled($type='')
    {
        return Mage::app()->useCache((empty($type) ? $this->_globalTags[0] : $type));
    }

    /**
     * Retrieve default cache lifetime. This function relays on the
     * cache configuration values for the particular extension, if
     * there are none the default is to return null, meaning no
     * specific lifetime is assigned and the cache will never expire.
     *
     * @return int|null|boolean
     */
    public function _getConfigLifetime()
    {
        $result = false;

        $v = Mage::getStoreConfig($this->_getModuleName() . '/cache/lifetime');

        if (!is_null($v) && intval($v) > 0) {
            $result = (int) $v;
        }

        return $result;
    }

    /**
     * Clear cache data
     *
     * @param   array|string $type
     * @param   string|null $value
     * @return  MageParts_Base_Helper_Cache
     */
    public function clean($type, $value=null)
    {
        try {
            $selection = array();

            if (is_array($type)) {
                $selection = $type;
            } else {
                $selection[] = array($type, $value);
            }

            if (count($selection)) {
                $clean = array();

                $cache = Mage::getModel('core/cache');

                foreach ($selection as $piece) {
                    if (is_array($piece)) {
                        $type = isset($piece[0]) ? $piece[0] : null;
                        $value = isset($piece[1]) ? $piece[1] : null;

                        switch ($type) {
                            case 'all':
                                $clean = array_merge($clean, $this->_globalTags);
                                break;
                            case 'tag':
                                $clean[] = $value;
                                break;
                            case 'id':
                                $cache->remove($value);
                                break;
                        }
                    }
                }

                $this->log("Cleaning out cached data:");
                $this->log($clean);

                $cache->clean(array_unique($clean));
            }
        } catch (Exception $e) {
            $this->log($e);
        }

        return $this;
    }

    /**
     * Check if debugger is enabled or not for cache functions.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return parent::isDebugEnabled() && Mage::getStoreConfigFlag('mageparts_base/debug/cache');
    }

    /**
     * Retrieve array of all global cache tags.
     *
     * @return array
     */
    public function getGlobalTags()
    {
        return $this->_globalTags;
    }

    /**
     * Retrieve array of cache tags, including globals.
     *
     * @param array $custom
     * @return array
     */
    public function getTags($custom=null)
    {
        $result = $this->_globalTags;

        if (!is_array($result)) {
            $result = array();
        }

        if (is_array($custom)) {
            $result = array_merge($result, $custom);
        } else if (is_string($custom)) {
            $result[] = $custom;
        }

        return $result;
    }

    /**
     * Get object cache tags.
     *
     * @param string $objectTag
     * @param int $id
     * @return array
     */
    public function getObjectTags($objectTag, $id)
    {
        return $this->getTags(array(
            $objectTag,
            $objectTag . '_' . $id
        ));
    }

    /**
     * Retrieve array of cache tags based on product.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $append
     * @return array
     */
    public function getProductTags(Mage_Catalog_Model_Product $product, $append = array())
    {
        // get global tags
        $result = $this->getTags();

        if ($product && $product->getId()) {
            // add parent product
            $result[] = Mage_Catalog_Model_Product::CACHE_TAG . '_' . $product->getId();

            // add child products
            if (in_array($product->getTypeId(), array('configurable', 'bundle', 'grouped'))) {
                $childIdCollection = $product->getTypeInstance(true)->getChildrenIds($product->getId());

                if (count($childIdCollection)) {
                    foreach ($childIdCollection as $idCollection) {
                        if (!is_array($idCollection)) {
                            $result[] = Mage_Catalog_Model_Product::CACHE_TAG . '_' . $idCollection;
                        } else if (count($idCollection)) {
                            foreach ($idCollection as $childId) {
                                $result[] = Mage_Catalog_Model_Product::CACHE_TAG . '_' . $childId;
                            }
                        }
                    }
                }
            }
        }

        if (count($append)) {
            $result = array_merge($result, $append);
        }

        return $result;
    }

    /**
     * Generate and return cache key.
     *
     * @param array $pieces
     * @param boolean $includeCommonData
     * @return string
     */
    public function generateCacheKey(array $pieces, $includeCommonData=true)
    {
        if ($includeCommonData) {
            $pieces['cache_type'] = $this->getGlobalTags();
        }

        return sha1(serialize($pieces));
    }

    /**
     * Encode cache data.
     *
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        $result = '';

        if ($data) {
            if (function_exists('json_encode') && function_exists('json_decode')) {
                $result = json_encode($data);
            } else {
                $result = serialize($data);
            }
        }

        return $result;
    }

    /**
     * Decode cache data.
     *
     * @param string $data
     * @return mixed
     */
    public function decode($data, $isArray=false)
    {
        $result = null;

        if ($data) {
            if ($this->isSerialized($data)) {
                $result = @unserialize($data);
            } else if (function_exists('json_decode')) {
                $result = @json_decode($data, $isArray);
            }
        }

        return $result;
    }

}

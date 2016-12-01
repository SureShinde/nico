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

class MageParts_Base_Helper_Url extends MageParts_Base_Helper_Data
{

    /**
     * Get current hostname including protocol scheme.
     *
     * @return string
     */
    public function getCurrentHost()
    {
        if (is_null($this->_currentHostname)) {
            $this->_currentHostname = $this->getCurrentProtocol() . $_SERVER['HTTP_HOST'];
        }

        return $this->_currentHostname;
    }

    /**
     * Retrieve current URL protocol.
     *
     * @return string
     */
    public function getCurrentProtocol()
    {
        return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://';
    }

    /**
     * Get current url.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (is_null($this->_currentUrl)) {
            $host = $this->getCurrentHost();
            $url = Mage::app()->getRequest()->getRequestUri();

            $this->_currentUrl = $this->getCleanUrl(((substr($url, 0, 1) == '/') || (substr(strrev($host), 0, 1) == '/')) ? ($host . $url) : ($host . '/' . $url));
        }

        return $this->_currentUrl;
    }

    /**
     * Return URL without trailing slash.
     *
     * @param string $url
     * @return string
     */
    public function getCleanUrl($url)
    {
        $url = substr(strrev($url), 0, 1) == '/' ? strrev(substr(strrev($url), 1)) : $url;
        $url = preg_replace('~(?<!https:|http:)[/\\\\]+~', "/", trim($url));
        return $url;
    }

    /**
     * Retrieve Module/Controller/Action
     *
     * @param boolean $includeModule
     * @param boolean $includeController
     * @param boolean $includeAction
     * @return string
     */
    public function getMca($includeModule=true, $includeController=true, $includeAction=true)
    {
        $result = '';

        if ($includeModule) {
            $result.= $this->_getRequest()->getModuleName();
        }

        if ($includeController) {
            $result.= !empty($result) ? '/' : '';
            $result.= $this->_getRequest()->getControllerName();
        }

        if ($includeAction) {
            $result.= !empty($result) ? '/' : '';
            $result.= $this->_getRequest()->getActionName();
        }

        return $result;
    }

    /**
     * Replace URL protocol with current used protocol.
     *
     * @param string $url
     * @return string
     */
    public function correctUrlProtocol($url)
    {
        if (!empty($url)) {
            $foundProtocol = false;

            foreach (array('http://', 'https://') as $p) {
                if (strpos($url, $p) === 0) {
                    $url = str_replace($p, $this->getCurrentProtocol(), $url);
                    $foundProtocol = true;
                    break;
                }
            }

            if (!$foundProtocol) {
                $url = $this->getCurrentProtocol() . $url;
            }
        }

        return $url;
    }

    /**
     * Retrieve protocol used in URL.
     *
     * @param string $url
     * @return string
     */
    public function getUrlProtocol($url)
    {
        $result = '';

        if (!empty($url)) {
            foreach (array('http://', 'https://') as $p) {
                if (strpos($url, $p) === 0) {
                    $result = $p;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve current URL without get parameters.
     *
     * @return string
     */
    public function getCurrentUrlWithoutParams()
    {
        return Mage::getBaseUrl() . $this->getMca();
    }

    /**
     * Check if requested URL exists within the given array of alternatives.
     *
     * @param array $arr
     * @param string $url
     * @return boolean
     */
    public function urlInArray($url='', array $arr)
    {
        $result = false;

        if (count($arr)) {
            foreach ($arr as $urlObj) {
                if (!$this->isAssoc($urlObj)) {
                    $urlObj = array(
                        'type' => 'absolute',
                        'url' => $urlObj
                    );
                }

                if ($urlObj['type'] == 'mca') {
                    $urlPieces = explode('/', $urlObj['url']);

                    if (count($urlPieces) && ($urlPieces[0] == '*' || Mage::app()->getRequest()->getModuleName() == $urlPieces[0]) && (!isset($urlPieces[1]) || $urlPieces[1] == '*' || Mage::app()->getRequest()->getControllerName() == $urlPieces[1]) && (!isset($urlPieces[2]) || $urlPieces[2] == '*' || Mage::app()->getRequest()->getActionName() == $urlPieces[2])) {
                        $result = true;
                        break;
                    }
                } else if ($urlObj['type'] == 'regex') {
                    if (preg_match($urlObj['url'], $url)) {
                        $result = true;
                        break;
                    }
                } else if ($urlObj['type'] == 'relative') {
                    $urlObj['url'] = $this->getCleanUrl(Mage::getBaseUrl() . $urlObj['url']);

                    if (strpos($this->getCleanUrl(strtok($url, '?')), $urlObj['url']) !== false) {
                        $result = true;
                        break;
                    }
                } else if ($urlObj['type'] == 'absolute') {
                    if ($url == $this->removeStringTrail($this->correctUrlProtocol($urlObj['url']), '/')) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve URL table from config and compare a URL to its content.
     *
     * @param string $configPath
     * @param int $storeId
     * @param string $url
     * @return boolean
     */
    public function urlInTable($configPath='', $url='', $storeId=null)
    {
        $result = false;

        if (!empty($configPath)) {
            $arr = Mage::getStoreConfig($configPath, (is_null($storeId) ? $this->getStoreId() : $storeId));

            if ($this->isSerialized($arr)) {
                $arr = @unserialize($arr);
            }

            if (empty($url)) {
                $url = $this->getCurrentUrl();
            }

            if (is_array($arr) && count($arr)) {
                $result = $this->urlInArray($url, $arr);
            }
        }

        return $result;
    }

}

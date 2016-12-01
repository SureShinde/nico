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
 * @package    MageParts_Requirelogin
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Requirelogin_Model_Observer_Adminhtml extends MageParts_Base_Model_Observer_Adminhtml
{

    /**
     * After applying changes to the configuration.
     *
     * @param Varien_Event_Observer $observer
     */
    public function changedCfg(Varien_Event_Observer $observer)
    {
        parent::changedCfg($observer);

        /* If a page has been selected to redirect registration requests from we should add that page to the exception
           list to avoid redirect looping (accessing registration page -> redirecting to cms page -> redirecting to
           login page). */
        $url = $this->_getHelper()->getRegistrationRedirectUrl();

        if (!empty($url)) {
            $row = array(
                'url'  => $this->_getHelper()->getRegistrationRedirectUrl(),
                'type' => 'absolute'
            );

            $this->_getHelper()->addRowToSerializedConfigArray('requirelogin/general/url_exception_list', $row);
        }
    }

    /**
     * Retrieve helper.
     *
     * @return MageParts_Requirelogin_Helper_Data
     */
    public function _getHelper()
    {
        return Mage::helper('requirelogin');
    }

}

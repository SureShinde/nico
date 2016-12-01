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

class MageParts_Requirelogin_Helper_Data extends MageParts_Base_Helper_Data
{

    /**
     * Define module name, used by various Base helper functions.
     *
     * @var string
     */
    protected $_moduleName = 'requirelogin';

    /**
     * Retrieve redirection URL for registration page.
     *
     * Explanation of $config variable:
     *
     * Whenever we save the configuration section in the backend (see adminhtml observer), we collect the registration
     * redirect URL and add it to the exception table, so people can actually access it without being redirect to the
     * login page instead. Since the config setting can differ on both store views and website, we have to set up the
     * $config variable in the function below to either target the specified store or website for our configuration
     * changes.
     *
     * @return string
     */
    public function getRegistrationRedirectUrl()
    {
        $result = '';

        if (Mage::getStoreConfigFlag('requirelogin/register/redirect_enabled') || $this->isAdmin()) {
            // Get place to read config from
            $config = $this->getCurrentStoreOrWebsite();

            $type = $config->getConfig('requirelogin/register/redirect_type');

            if ($type == 'cms_page') {
                $pageId = $config->getConfig('requirelogin/register/redirect_cms_page');

                if (!empty($pageId)) {
                    // check if id includes a delimiter
                    $delPos = strrpos($pageId, '|');

                    // get page id by delimiter position
                    if ($delPos) {
                        $pageId = substr($pageId, 0, $delPos);
                    }

                    $result = Mage::getUrl($pageId);
                }
            } else if ($type == 'custom_url') {
                $result = $config->getConfig('requirelogin/register/redirect_custom_url');
            }
        }

        return $result;
    }

}

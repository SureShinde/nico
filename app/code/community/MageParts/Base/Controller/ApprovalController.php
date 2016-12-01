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

class MageParts_Base_Controller_ApprovalController extends Mage_Core_Controller_Front_Action
{

    /**
     * The helper type used by this class.
     *
     * @var string
     */
    protected $_helper;

    /**
     * The model type we are applying status updates to.
     *
     * @var string
     */
    protected $_model;

    /**
     * Reference name used in messages and log entries, for example customer/customer can be "customer account" or just
     * "account", generating strings like "Unable to approve _account_: _account_ does not exist".
     *
     * @var string
     */
    protected $_objectReference;

    /**
     * POST / GET parameter mappings. Normally you would never change these.
     *
     * @var array
     */
    protected $_params = array(
        'id'     => 'id',
        'code'   => 'code',
        'status' => 'status'
    );

    /**
     * Model field mappings (approval_code / status may be occupied by other values in some cases).
     *
     * @var array
     */
    protected $_fields = array(
        'approval_code'   => 'approval_code',
        'approval_status' => 'approval_status'
    );

    /**
     * Update model approval status.
     */
    public function updateStatusAction()
    {
        $id = (int) $this->getRequest()->getParam($this->_params['id']);
        $status = (int) $this->getRequest()->getParam($this->_params['status']);
        $code = $this->getRequest()->getParam($this->_params['code']);

        try {
            // debug entry
            $this->_getHelper()->log("Approval attempt of id " . $id . ", code " . $code);

            if (is_null($this->_helper)) {
                throw new Exception("Please identify a helper.");
            }

            if (is_null($this->_model)) {
                throw new Exception("Please identify a model.");
            }

            if (is_null($this->_objectReference)) {
                throw new Exception("Please identify an object reference.");
            }

            if (!$id || !$code) {
                throw new Exception("missing input parameters.");
            }

            $model = Mage::getModel($this->_model)->load($id);

            if (!$model || !$model->getId()) {
                throw new Exception($this->_objectReference . " does not exist.");
            }

            if (!$model->getData($this->_fields['approval_code'])) {
                throw new Exception("this " . $this->_objectReference . " has no approval code, perhaps another admin already handled the request?");
            }

            if ($model->getData($this->_fields['approval_code']) !== $code) {
                throw new Exception("code mismatch.");
            }

            $model->setData($this->_fields['approval_status'], $status)
                ->setData($this->_fields['approval_code'], null)
                ->save();

            $this->_getHelper()->getCustomerSession()->addSuccess($this->_getHelper()->__(ucfirst($this->_objectReference) . " approval status successfully updated!"));
        } catch (Exception $e) {
            $this->_getHelper()->log(("Unable to update approval status of " . $this->_objectReference . ": ") . $e->getMessage());
            $this->_getHelper()->getCustomerSession()->addError($this->_getHelper()->__("Something went wrong, please check the log file for more information."));
        }

        $this->_getHelper()->redirect(Mage::getBaseUrl(), true);
    }

    /**
     * Retrieve helper object.
     *
     * @return MageParts_Base_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper($this->_helper);
    }

}

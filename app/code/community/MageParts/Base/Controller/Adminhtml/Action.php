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

class MageParts_Base_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{

    /**
     * Id parameter, used to load the model associated with this controller.
     *
     * @var string
     */
    protected $_idParam = 'id';

    /**
     * Name of model associated with this controller.
     *
     * @var string
     */
    protected $_modelName = '';

    /**
     * Key names in the registry where the model instance will be stored once created.
     *
     * @var array
     */
    protected $_modelRegistryNames = array();

    /**
     * Active menu path.
     *
     * @var string
     */
    protected $_activeMenu = '';

    /**
     * Breadcrumbs.
     *
     * @var array
     */
    protected $_breadcrumbs = array();

    /**
     * Title segments.
     *
     * @var array
     */
    protected $_titleSegments = array();

    /**
     * ACL path for isAllowed function.
     *
     * @var string
     */
    protected $_aclPath = '';

    /**
     * Redirect back if an error occurs while saving the record.
     *
     * @var boolean
     */
    protected $_redirectBackOnSaveError = true;

    /**
     * Redirect back if an error occurs while deleting the record.
     *
     * @var boolean
     */
    protected $_redirectBackOnDeleteError = true;

    /**
     * Instance of model related to this controller.
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_model = null;

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        if (empty($this->_aclPath)) {
            $this->_aclPath = $this->_activeMenu;
        }
    }

    /**
     * Initializes model associated with controller if any.
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _initModel()
    {
        if (is_null($this->_model) && !empty($this->_modelName) && !empty($this->_idParam)) {
            // get requested id
            $id = $this->_getRequestedId();

            // load object by id
            $model = Mage::getModel($this->_modelName);

            if ($id) {
                try {
                    $model->load($id);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // set entered data if was error when we do save
            $formData = $this->_getSession()->getFormData(true);

            if (!empty($formData)) {
                $model->setData($formData);
            }

            // register loaded object
            if (is_array($this->_modelRegistryNames) && count($this->_modelRegistryNames)) {
                foreach ($this->_modelRegistryNames as $key) {
                    Mage::register($key, $model);
                }
            } else {
                $key = (string) $this->_modelRegistryNames;

                if (!empty($key)) {
                    Mage::register($key, $model);
                }
            }

            $this->_model = $model;
        }

        return $this->_model;
    }

    /**
     * Sets active menu, creates breadcrumbs etc.
     *
     * @return MageParts_Base_Controller_Adminhtml_Action
     */
    public function _initAction()
    {
        if (!empty($this->_activeMenu)) {
            $this->loadLayout()->_setActiveMenu($this->_activeMenu);
        }

        if (is_array($this->_breadcrumbs) && count($this->_breadcrumbs)) {
            foreach ($this->_breadcrumbs as $breadcrumb) {
                if (is_array($breadcrumb) && isset($breadcrumb['label']) && isset($breadcrumb['title'])) {
                    $this->loadLayout()->_addBreadcrumb($breadcrumb['label'], $this->_getHelper()->__($breadcrumb['title']));
                } else {
                    // Translate
                    $breadcrumb = $this->_getHelper()->__($breadcrumb);

                    $this->loadLayout()->_addBreadcrumb($breadcrumb, $breadcrumb);
                }
            }
        }

        return $this;
    }

    /**
     * Index action.
     */
    public function indexAction()
    {
        if (is_array($this->_titleSegments) && count($this->_titleSegments)) {
            foreach ($this->_titleSegments as $titleSegment) {
                $this->_title($this->_getHelper()->__($titleSegment));
            }
        }

        $this->_initAction()->renderLayout();
    }

    /**
     *  When creating a new entry.
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * When editing an existing record.
     */
    public function editAction()
    {
        $model = $this->_initModel();

        // Make sure that the model exists before continuing
        if ($this->_getRequestedId() && !$model && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This ' . $this->_getObjectLabel() . ' no longer exist or is corrupt.'));
            $this->_redirect('*/*/');
        } else {
            $this->_initAction()->renderLayout();
        }
    }

    /**
     * Retrieve requested id for associated model instance.
     *
     * @return int
     */
    public function _getRequestedId()
    {
        return (int) $this->getRequest()->getParam($this->_idParam);
    }

    /**
     * When saving a record.
     */
    public function saveAction()
    {
        $model = $this->_initModel();

        $redirectBack = false;

        if ($data = $this->getRequest()->getPost()) {
            try {
                $this->_beforeSaveAction($model, $data);

                $model->addData($data)
                    ->save();

                $this->_afterSaveAction($model, $data);

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__($this->_getObjectLabel(true) . ' successfully saved.'));
            } catch (Exception $e) {
                $redirectBack = $this->_redirectBackOnSaveError;
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage())->setFormData($data);
                Mage::logException($e);
            }
        }

        if ($redirectBack && $model && $model->getId()) {
            $this->_redirect('*/*/edit', array(
                $this->_idParam => $model->getId(),
                '_current' => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Deleting a record.
     */
    public function deleteAction()
    {
        $model = $this->_initModel();

        $redirectBack = false;

        try {
            $this->_beforeDeleteAction($model, $data);

            $model->delete();

            $this->_afterDeleteAction($model, $data);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($this->_getObjectLabel(true) . ' successfully deleted.'));
        } catch (Exception $e) {
            $redirectBack = $this->_redirectBackOnDeleteError;
            Mage::getSingleton('adminhtml/session')->addError($this->__('Missing or invalid ' . $this->_getObjectLabel() . ' record.'));
            Mage::logException($e);
        }

        if ($redirectBack && $model && $model->getId()) {
            $this->_redirect('*/*/edit', array(
                $this->_idParam => $model->getId(),
                '_current' => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Check permissions before executing an action.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $result = true;

        if (!empty($this->_aclPath)) {
            $path = null;

            if (is_array($this->_aclPath)) {
                if (isset($this->_aclPath['base'])) {
                    $actionPath = strtolower($this->getRequest()->getActionName());

                    if (isset($this->_aclPath[$actionPath])) {
                        $path = $this->_aclPath[$actionPath];
                    } else {
                        $path = $this->_aclPath['base'];
                    }
                }
            } else {
                $path = $this->_aclPath;
            }

            if (!is_null($path)) {
                $result = Mage::getSingleton('admin/session')->isAllowed($path);
            }
        }

        return $result;
    }

    /**
     * Executes before the model associated with this controller is saved (meaning before ->save() is invoked on the
     * object).
     *
     * This is a structural (interface) function meant to be used by child classes.
     *
     * @param $model Mage_Core_Model_Abstract Instance of model used by requested action.
     * @param $data array Data submitted to the requested action.
     * @return MageParts_Base_Controller_Adminhtml_Action
     */
    public function _beforeSaveAction(&$model, &$data)
    {
        return $this;
    }

    /**
     * Executes after the model associated with this controller is saved (meaning after ->save() is invoked on the
     * object).
     *
     * This is a structural (interface) function meant to be used by child classes.
     *
     * @param $model Mage_Core_Model_Abstract Instance of model used by requested action.
     * @param $data array Data submitted to the requested action.
     * @return MageParts_Base_Controller_Adminhtml_Action
     */
    public function _afterSaveAction(&$model, &$data)
    {
        return $this;
    }

    /**
     * Executes before the model associated with this controller is deleted (meaning before ->delete() is invoked on the
     * object).
     *
     * This is a structural (interface) function meant to be used by child classes.
     *
     * @param $model Mage_Core_Model_Abstract Instance of model used by requested action.
     * @param $data array Data submitted to the requested action.
     * @return MageParts_Base_Controller_Adminhtml_Action
     */
    public function _beforeDeleteAction(&$model, &$data)
    {
        return $this;
    }

    /**
     * Executes after the model associated with this controller is deleted (meaning after ->delete() is invoked on the
     * object).
     *
     * This is a structural (interface) function meant to be used by child classes.
     *
     * @param $model Mage_Core_Model_Abstract Instance of model used by requested action.
     * @param $data array Data submitted to the requested action.
     * @return MageParts_Base_Controller_Adminhtml_Action
     */
    public function _afterDeleteAction(&$model, &$data)
    {
        return $this;
    }

    /**
     * Get object label from model name.
     *
     * @return string
     */
    public function _getObjectLabel($capitalize=false)
    {
        $result = 'object';

        if (!empty($this->_modelName)) {
            $result = preg_replace('/\_/', ' ', substr($this->_modelName, strpos($this->_modelName, '/')+1));
        }

        if ($capitalize) {
            $result = ucfirst($result);
        }

        return $result;
    }

    /**
     * Retrieve helper object.
     *
     * Child classes may implement their own variation of this function, mainly for proper translation support.
     *
     * @return MageParts_Base_Helper_Data
     */
    public function _getHelper()
    {
        return Mage::helper('mageparts_base');
    }

}

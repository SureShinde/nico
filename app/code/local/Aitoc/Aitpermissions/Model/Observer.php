<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.9.2
 * @license:     QvHpbEc0oOzK3qo2hlmyDcWVqjqnkaNA9m9UogV4wV
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Model_Observer
{
    private $_role = null;
    
    protected $_helper;
    protected $_helperAccess;

    protected function _addFilterAllowOwnProducts($collection)
    {
        $idSubAdmin = Mage::getSingleton('admin/session')->getUser()->getId();
        
        $collection->addAttributeToFilter('created_by', $idSubAdmin);
        
        return $collection;
    }

    public function __construct()
    {
        $this->_helper = Mage::helper('aitpermissions');
        $this->_helperAccess = Mage::helper('aitpermissions/access');
    }

    private function _getCurrentRole()
    {
        if (null == $this->_role)
        {
            $this->_role = Mage::getSingleton('aitpermissions/role');
        }

        return $this->_role;
    }

    public function onAdminRolesSaveBefore(Varien_Event_Observer $observer)
    {
        $scope = Mage::app()->getRequest()->getPost('access_scope');
        
        if ('store' == $scope)
        {
            $request = Mage::app()->getRequest();

            $storeIds = $request->getPost('store_switcher');
            $categoryIds = $request->getPost('store_category_ids');
            $errorStoreIds = array();

            foreach ($storeIds as $storeId => $storeviewIds)
            {
                if (empty($categoryIds[$storeId]))
                {
                    $errorStoreIds[] = $storeId;
                }
            }

            if ($errorStoreIds)
            {
                $storesCollection = Mage::getModel('core/store_group')->getCollection()
                    ->addFieldToFilter('group_id', array('in' => $errorStoreIds));

                $storeNames = array();
                foreach ($storesCollection as $store)
                {
                    $storeNames[] = $store->getName();
                }

                Mage::throwException(
                    $this->_helper->__(
                        'Please, select allowed categories for the following stores: %s',
                        join(', ', $storeNames)
                ));
            }
        }
    }

    public function onAdminRolesSaveAfter($observer)
    {
        $roleId = $observer->getObject()->getId();
        $scope = Mage::app()->getRequest()->getPost('access_scope');

        if ($roleId && $scope)
        {
            $this->deleteAdvancedRole($roleId);

            if ('store' == $scope)
            {
                $this->_saveRoleStoreRestriction($roleId);
                $this->_saveRoleProductCreatorPermissions($roleId);
                $this->_saveRoleProductTabsPermissions($roleId);
            }
            else if ('website' == $scope)
            {
                $this->_saveRoleWebsiteRestriction($roleId);
                $this->_saveRoleProductCreatorPermissions($roleId);
                $this->_saveRoleProductTabsPermissions($roleId);
            }
            else if ('disabled' == $scope)
            {
//                Mage::getSingleton('adminhtml/session')->addSuccess(
//                    $this->_helper->__('Advanced permissions were disabled for this role')
//                );
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_helper->__('Invalid Advanced Permissions scope type received')
                );
            }
        }

        $this->_saveAttributeArray('is_allow_ids',$roleId, 1);
        $this->_saveAttributeArray('is_disable_ids',$roleId, 0);
    }

    protected function _saveAttributeArray($name,$roleId, $allow)
    {
        if(Mage::app()->getRequest()->getPost($name) != Mage::app()->getRequest()->getPost($name.'_old'))
        {
            $attributes = $this->_getArrayByName($name);
            $attributesOld = $this->_getArrayByName($name.'_old');

            $arrayAdd = array_diff($attributes,$attributesOld);
            $arrayDel = array_diff($attributesOld,$attributes);

            $collection = Mage::getModel('aitpermissions/editor_attribute')->getCollection();
            $collection->setPostRoleId($roleId);
            $collection->setPostAllow($allow);

            if(!empty($arrayDel))
            {
                $collection->deleteAttributeByRole($arrayDel);
            }
            if(!empty($arrayAdd))
            {
                $collection->addAttributeByRole($arrayAdd);
            }
        }
    }

    protected function _getArrayByName($name)
    {
        $array = Mage::app()->getRequest()->getPost($name);
        parse_str($array, $array);
        $array = array_keys($array);
        return $array;
    }

    private function _saveRoleStoreRestriction($roleId)
    {
        $request = Mage::app()->getRequest();

        $canUpdateGlobalAttr = (int)$request->getPost('allowupdateglobalattrs');
        $canEditOwnProductsOnly = (int)$request->getPost('caneditownproductsonly');
        $canCreateProduct = (int) $request->getPost('allow_create_product');

        $selectedStoreIds = $request->getPost('store_switcher');
        $storeCategoryIds = $request->getPost('store_category_ids');

        foreach ($selectedStoreIds as $storeId => $storeviewIds)
        {
            $storeviewIds = implode(',', $storeviewIds);

            $categoryIds = '';
            if (isset($storeCategoryIds[$storeId]))
            {
                $categoryIds = implode(',', array_diff(array_unique(explode(',', $storeCategoryIds[$storeId])), array('')));
            }

            $advancedrole = Mage::getModel('aitpermissions/advancedrole');

            $advancedrole->setData('role_id', $roleId);
            $advancedrole->setData('store_id', $storeId);
            $advancedrole->setData('storeview_ids', $storeviewIds);
            $advancedrole->setData('category_ids', $categoryIds);
            $advancedrole->setData('website_id', 0);
            $advancedrole->setData('can_edit_global_attr', $canUpdateGlobalAttr);
            $advancedrole->setData('can_edit_own_products_only', $canEditOwnProductsOnly);
            $advancedrole->setData('can_create_products', $canCreateProduct);

            $advancedrole->save();
        }
    }

    private function _saveRoleWebsiteRestriction($roleId)
    {
        $request = Mage::app()->getRequest();

        $canUpdateGlobalAttr = (int)$request->getPost('allowupdateglobalattrs');
        $canEditOwnProductsOnly = (int)$request->getPost('caneditownproductsonly');
        $canCreateProduct = (int) $request->getPost('allow_create_product');

        foreach ($request->getPost('website_switcher') as $websiteId)
        {
            $advancedrole = Mage::getModel('aitpermissions/advancedrole');

            $advancedrole->setData('role_id', $roleId);
            $advancedrole->setData('website_id', $websiteId);
            $advancedrole->setData('store_id', '');
            $advancedrole->setData('category_ids', '');
            $advancedrole->setData('can_edit_global_attr', $canUpdateGlobalAttr);
            $advancedrole->setData('can_edit_own_products_only', $canEditOwnProductsOnly);
            $advancedrole->setData('can_create_products', $canCreateProduct);

            $advancedrole->save();
        }
    }

    protected function _saveRoleProductCreatorPermissions($roleId)
    {
        $request = Mage::app()->getRequest();        
        
        if($request->getPost('apply_to'))
        {            
            $types = $request->getPost('apply_to');
            foreach($types as $type)
            {
                $editorType = Mage::getModel('aitpermissions/editor_type');
                $editorType->setData('role_id', $roleId);
                $editorType->setData('type', $type);
                $editorType->save();
            }
        }
    }

    protected function _saveRoleProductTabsPermissions($roleId)
    {
        $post = Mage::app()->getRequest()->getPost();
        $tabs = Mage::helper('aitpermissions')->getProductTabs();
        $tabs = array_keys($tabs);
        if(!isset($post) || !is_array($post))
        {
            return;
        }

        foreach($tabs as $tab)
        {
            if(array_key_exists($tab, $post))
            {
                $editorType = Mage::getModel('aitpermissions/editor_tab');
                $editorType->setData('role_id', $roleId);
                $editorType->setData('tab_code', $tab);
                $editorType->save();
            }
        }
    }

    public function deleteAdvancedRole($roleId)
    {
        Mage::getModel('aitpermissions/advancedrole')->deleteRole($roleId);
        Mage::getModel('aitpermissions/editor_type')->deleteRole($roleId);
        Mage::getModel('aitpermissions/editor_tab')->deleteRole($roleId);
    }

    public function onAdminRolesDeleteAfter($observer)
    {
        $role = $observer->getObject();

        if ($role)
        {
            $this->deleteAdvancedRole($role->getId());
        }
    }

	public function onCatalogProductValidateBefore($observer)
    {
        if (!Mage::getSingleton('admin/session')->getUser()) {
            return;
        }
        $currentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getId();
        if (!Mage::getModel('aitpermissions/advancedrole')->load($currentUserRole, 'role_id')->getId() || Mage::app()->isSingleStoreMode())
        {
            return;
        }

        $request = Mage::app()->getFrontController()->getRequest();
        $productData = $request->getPost('product');
        if (isset($productData) && !isset($productData['website_ids'])) {
            Mage::throwException($this->_helper->__('The product must be assigned to at least one website.'));
        }
    }

    /**
     * Validate if current category can have "Add Subcategory" button
     *
     * @var Varien_Object $observer
     */
    public function onCatalogCategoryTreeCanAddSubCategory($observer)
    {
        $category = $observer->getCategory();
        $options = $observer->getOptions();
        if(!$category || $options->getIsAllow()!= true) {
            return;
        }
        $role = Mage::getSingleton('aitpermissions/role');
        if($role->isPermissionsEnabled() && !in_array($category->getId(), $role->getAllowedCategoryIds())) {
            $options->setIsAllow(false);
        }
    }

    public function onControllerActionLayoutRenderBeforeAdminhtmlCatalogProductEdit($observer)
    {
        $role = $this->_getCurrentRole();

        if (!$role->isPermissionsEnabled())
        {
            return;
        }

        $tabsBlock = Mage::app()->getLayout()->getBlock('product_tabs');
        $tabsHide = $this->_getHideTabs();
        if(!empty($tabsBlock) && !empty($tabsHide))
        {
            foreach($tabsHide as $tab)
            {
                $tabsBlock->setTabData($tab, 'is_hidden', true);
            }
        }
    }

    protected function _getHideTabs()
    {        
        $roleId = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
        $disabledTabs = Mage::getModel('aitpermissions/editor_tab')->getDisabledTabs($roleId);
        if($disabledTabs)
        {
            return $disabledTabs;
        }
        
        return array();
    }

	
    public function onCatalogProductEditAction($observer)
    {
        $role = $this->_getCurrentRole();

        if (!$role->isPermissionsEnabled())
        {
            return;
        }

        $product = $observer->getProduct();

        if (($role->canEditOwnProductsOnly() && !$role->isOwnProduct($product)) ||
            !$role->isAllowedToEditProduct($product))
        {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->_helper->__(
                    'Sorry, you have no permissions to edit this product. For more details please contact site administrator.'
            ));

            $controller = Mage::app()->getFrontController();
            $controller->getResponse()
                ->setRedirect(Mage::getModel('adminhtml/url')->getUrl(
                    '*/*/',
                    array('store' => $controller->getRequest()->getParam('store', 0))))
                ->sendResponse();

            exit;
        }

        if (!$role->isAllowedToDelete())
        {
            $product->setIsDeleteable(false);
        }
    }
    
    public function onCatalogProductPrepareSave($observer)
    {
        // should check if the product is a new one
        $product = $observer->getProduct();
        $request = $observer->getRequest();

        $productData = $request->getPost('product');

        if (!$product->getId())
        {
            // new product
            Mage::getSingleton('catalog/session')->setIsNewProduct(true);
            Mage::getSingleton('catalog/session')->setSelectedVisibility($productData['visibility']);
            $product->setData('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        }
    }

/**
 * @refactor
 * add 3 sub methods
 */
    public function onCatalogProductSaveAfter($observer)
    {
        if (!Mage::getSingleton('admin/session')->getUser())
        {
            return; //it happens only when we save products by API, #28943
        }        

        $currentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getId();
        if (!Mage::getModel('aitpermissions/advancedrole')->load($currentUserRole, 'role_id')->getId())
        {
            $this->_updateVisibilityBySuperadmin($observer);
            return;
        }
        
        $controllerName = Mage::app()->getRequest()->getControllerName();

        if (!(Mage::getSingleton('catalog/session')->getIsNewProduct(true)) &&
            !('catalog_product' == $controllerName && Mage::helper('aitpermissions')->isQuickCreate()))
        {
            return;
        }
        
        $product = $observer->getProduct();

        // setting created by attribute
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();
        
        if (!('catalog_product' == $controllerName && Mage::helper('aitpermissions')->isQuickCreate()))
        {

            $product->addAttributeUpdate('created_by', $adminId, 0);
        }
        
        $role = $this->_getCurrentRole();
        
        if (!$role->isPermissionsEnabled())
        {
            return;
        }
        
        // setting selected visibility for allowed store views
        
        $allowedStoreviewIds = $role->getAllowedStoreviewIds();
        $visibility = Mage::getSingleton('catalog/session')->getSelectedVisibility(true);
        
        if(Mage::helper('aitpermissions')->isQuickCreate() && $visibility == null)
        {
            $visibility = $product->getData('visibility');
        }

        foreach ($allowedStoreviewIds as $allowedStoreviewId)
        {
            $product->addAttributeUpdate('visibility', $visibility, $allowedStoreviewId);
        }

        // setting visibility "Nowhere" for all other store views

        $visibilityNotVisible = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
        foreach (Mage::getModel('core/store')->getCollection() as $store)
        {
            if (0 != $store->getId() && (!in_array($store->getId(), $allowedStoreviewIds)))
            {
                $product->addAttributeUpdate('visibility', $visibilityNotVisible, $store->getId());
            }
        }
    }
    
    protected function _updateVisibilityBySuperadmin($observer)
    {
    	$product = $observer->getProduct();    	
        $currentStoreView = Mage::app()->getRequest()->getParam('store');

        if(isset($currentStoreView))
        {
    	    $this->_updateStoreViewVisibilityAttr($product);// Admin changes product visibility for one store view
        }
        else
        {
            $this->_updateAllStoresVisibilityAttr($product);// Admin imports product data from one website to another or saves product in default scope
        }
    }

   /*
    * @param Mage_Catalog_Model_Product $product
   */
    protected function _updateStoreViewVisibilityAttr($product)
    {
        $visibility = $product->getData('visibility');
        $currentStoreView = Mage::app()->getRequest()->getParam('store');
        $store = Mage::getModel('core/store')->load($currentStoreView);
        if($store->getId() && !$this->_toUseDefaultVisibilityAttr())
        {
            $this->updateAttributeValue(
                $store->getId(),
                $product->getId(),
                array('visibility' => $visibility)
            );
        }
    }

   /*
    * @param Mage_Catalog_Model_Product $product
   */
    protected function _updateAllStoresVisibilityAttr($product)
    {
        $visibility = $product->getData('visibility');
        $allowedStoreviewIds = $product->getStoreIds();
        foreach (Mage::getModel('core/store')->getCollection() as $store)
        {
            if (0 != $store->getId() && (in_array($store->getId(), $allowedStoreviewIds)))
            {
                $storeProduct = Mage::getModel('catalog/product')->setStoreId($store->getId());
                $storeProduct->load($product->getId());
                $storeVisibility = $storeProduct->getData('visibility');

                if($visibility != $storeVisibility)// change visibility value only for storeviews without 'use_default' setting
                {
                    $this->updateAttributeValue(
                        $store->getId(),
                        $product->getId(),
                        array('visibility' => $storeVisibility)
                    );
                }
            }
        }
    }

    protected function _toUseDefaultVisibilityAttr()
    {
        $useDefaults = Mage::app()->getRequest()->getPost('use_default');
        if (isset($useDefaults) && is_array($useDefaults)) {
            foreach ($useDefaults as $attributeCode) {
                if($attributeCode == 'visibility')
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function onCatalogProductCollectionLoadBefore($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $routeName = Mage::app()->getFrontController()->getRequest()->getRouteName();

        if (false === strpos($routeName, 'adminhtml') && false === strpos($routeName, 'bundle'))
        {
            return;
        }

        $getCurrentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
        $bCanEditOwn = Mage::getModel('aitpermissions/advancedrole')->canEditOwnProductsOnly($getCurrentUserRole);

        if ($bCanEditOwn === false && Mage::getStoreConfig('admin/general/showallproducts') == true)
        {
            return;
        }

        $role = $this->_getCurrentRole();
        $collection = $observer->getCollection();

        $controllerName = Mage::app()->getRequest()->getControllerName();

        if ($role->isScopeStore() &&
            !in_array($controllerName, array('sales_order_edit', 'sales_order_create')))
        {
            $collection->getSelect()->joinLeft(array(
                'product_cat' => $collection->getTable('catalog/category_product')),
                'product_cat.product_id = e.entity_id',
                array()
            );

            if (Mage::helper('aitpermissions')->isShowingProductsWithoutCategories() == 1)
            {
                $collection->getSelect()->where(
                    ' product_cat.category_id in (' . join(',', $role->getAllowedCategoryIds()) . ')
                or product_cat.category_id IS NULL '
                );
            }
            else
            {
                $collection->getSelect()->where(
                    ' product_cat.category_id in (' . join(',', $role->getAllowedCategoryIds()) . ')'
                );
            }

            $collection->getSelect()->distinct(true);
        }
        
        if ($role->isScopeWebsite())
        {
            $websiteIds = $role->getAllowedWebsiteIds();
            $scopeStoreId = Mage::app()->getFrontController()->getRequest()->getParam('store');

            if ($scopeStoreId)
            {
                $scopeWebsiteId = Mage::getModel('core/store')->load($scopeStoreId)->getWebsiteId();

                if (in_array($scopeWebsiteId, $websiteIds))
                {
                    $websiteIds = array($scopeWebsiteId);
                }
            }
            
            $collection->addWebsiteFilter($websiteIds);
        }

        if ($bCanEditOwn === true)
        {
            $this->_addFilterAllowOwnProducts($collection);
        }

    }
    
    protected function _filterCollectionByPermissions($collection)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        if ($collection->getFlag('permissions_processed'))
        {
            return;
        }

        if (false !== strpos(Mage::app()->getFrontController()->getRequest()->getRouteName(), 'adminhtml'))
        {
            $role = $this->_getCurrentRole();

            if ($role->isPermissionsEnabled())
            {
                if (version_compare(Mage::getVersion(), '1.4.1.0', '>'))
                {
                    $collection->addAttributeToFilter(
                        'main_table.store_id',
                        array('in' => $role->getAllowedStoreviewIds())
                    );
                }
                else
                {
                    $collection->addAttributeToFilter(
                        'store_id',
                        array('in' => $role->getAllowedStoreviewIds())
                    );
                }
            }
        }
        
        $collection->setFlag('permissions_processed', true);
    }

    private function _filterCustomerCollection($collection)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        if (!Mage::getStoreConfig('admin/general/showallcustomers'))
        {
            $collection->addAttributeToFilter(
                'website_id',
                array('in' => $this->_getCurrentRole()->getAllowedWebsiteIds())
            );
        }
    }

    private function _filterCmsMysql4BlockCollection($collection)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $table = Mage::getSingleton('core/resource')->getTableName('cms/block_store');

        $collection
            ->getSelect()
            ->distinct()
            ->join($table, $table . '.block_id = main_table.block_id', array());

        $request = Mage::app()->getRequest();

        if ($request->getParam('store'))
        {
            $storeId = Mage::getModel('core/store')
                ->load($request->getParam('store'))
                ->getId();

            $collection
                ->getSelect()
                ->where($table . '.store_id in (0, ' . $storeId . ')');
        }
        else
        {
            $allowedStoreviewIds = $this->_getCurrentRole()->getAllowedStoreviewIds();
            $collection
                ->getSelect()
                ->where($table . '.store_id in (0,' . implode(',', $allowedStoreviewIds) . ')');
        }
    }

    private function _filterUrlRewriteCollection($collection)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();

        if ('urlrewrite' == $controllerName &&
            'index' == $actionName)
        {
            $collection->addStoreFilter($this->_getCurrentRole()->getAllowedStoreviewIds());
        }
    }

    public function onEavCollectionAbstractLoadBefore($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $collection = $observer->getCollection();

        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }
        
        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Invoice_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Invoice_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }
        
        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Shipment_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Shipment_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }
        
        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Creditmemo_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Creditmemo_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }
        
        if ($collection instanceof Mage_Customer_Model_Entity_Customer_Collection
            || $collection instanceof Mage_Customer_Model_Resource_Customer_Collection)
        {
            $this->_filterCustomerCollection($collection);
        }
    }
    
    public function onCoreCollectionAbstractLoadBefore($observer)
    {
        $collection = $observer->getCollection();

        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Grid_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Grid_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }

        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Invoice_Grid_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }

        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Shipment_Grid_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }

        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Creditmemo_Grid_Collection
            || $collection instanceof Mage_Sales_Model_Resource_Order_Creditmemo_Grid_Collection)
        {
            $this->_filterCollectionByPermissions($collection);
        }

        if ($collection instanceof Mage_Core_Model_Resource_Url_Rewrite_Collection ||
			$collection instanceof Mage_Core_Model_Mysql4_Url_Rewrite_Collection)
        {
            $this->_filterUrlRewriteCollection($collection);
        }

        if ($collection instanceof Mage_Cms_Model_Mysql4_Block_Collection)
        {
            $this->_filterCmsMysql4BlockCollection($collection);
        }        
    }
    
    public function onSalesOrderLoadAfter($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        if (false !== strpos(Mage::app()->getFrontController()->getRequest()->getRouteName(), 'adminhtml'))
        {
            if (!in_array(
                $observer->getOrder()->getStoreId(),
                $this->_getCurrentRole()->getAllowedStoreviewIds()))
            {
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/sales_order'));
            }
        }
    }
    
    public function onCustomerLoadAfter($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled() ||
            Mage::getStoreConfig('admin/general/showallcustomers') ||
            Mage::getStoreConfig('customer/account_share/scope') == 0) //when enabled following mode, one account can be used for all magento stores
        {
            return;
        }

        $customer = $observer->getCustomer();
        
        if (false !== strpos(Mage::app()->getFrontController()->getRequest()->getRouteName(), 'adminhtml') &&
            $customer->getWebsiteId())
        {
            if (!in_array(
                $customer->getWebsiteId(),
                $this->_getCurrentRole()->getAllowedWebsiteIds()))
            {
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/*'));
            }
        }
    }
    
    public function onCmsPageLoadAfter($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $model = $observer->getObject();
        if ($model instanceof Mage_Cms_Model_Page)
        {
            if (!$model->getData('store_id'))
            {
                return;
            }

            if (is_array($model->getData('store_id')) &&
                in_array(0, $model->getData('store_id')))
            {
                // allow, if admin store (all store views) selected
                
                return;
            }

            if (is_array($model->getData('store_id')) && 
                array_intersect($model->getData('store_id'), $this->_getCurrentRole()->getAllowedStoreviewIds()))
            {
                return;
            }

            // if no permissions - redirect

            Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/*'));
        }
    }
    
    public function onAdminhtmlCmsPageEditTabMainPrepareForm($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $page = Mage::registry('cms_page');
        $pageStoreviewIds = (array)$page->getStoreId();

        // if page assigned to some storeview admin don't have access to - forbid enabled/disabled setting changes

        if (array_diff($pageStoreviewIds, $this->_getCurrentRole()->getAllowedStoreviewIds()))
        {
            $fieldset = $observer->getForm()->getElement('base_fieldset');
            $fieldset->removeField('is_active');
        }
    }
    
    public function onCmsPagePrepareSave($observer)
    {
        $page = $observer->getPage();
        if ($page->getId() && $this->_getCurrentRole()->isPermissionsEnabled())
        {
            // should keep in mind we may have store views from another websites (not visible on edit form) assigned
            $this->_helperAccess->setCmsObjectStores($page);
        }
    }
    
    public function onModelSaveBefore($observer)
    { 
        $model = $observer->getObject();

        if (($model instanceof Mage_Cms_Model_Block) ||
            ($model instanceof Mage_Widget_Model_Widget_Instance) ||
            ($model instanceof Mage_Poll_Model_Poll))
        {
            $this->_helperAccess->setCmsObjectStores($model);
        }

        if ($model instanceof Mage_Catalog_Model_Product)
        {
            $this->_helperAccess->setProductWebsites($model);
        }
    }
    
    public function onReviewDeleteBefore($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $reviewId = $observer->getObject()->getId();
        $reviewStoreId = Mage::getModel('review/review')->load($reviewId)->getData('store_id');

        if (!in_array($reviewStoreId, $this->_getCurrentRole()->getAllowedStoreviewIds()))
        {
            Mage::throwException($this->_helper->__('Review could not be deleted due to insufficent permissions.'));
        }
    }

    /**
     * If we have encountered an error when saving category - we need to clear post session data, because this category is not allowed to be edited
     */
    public function onAdminhtmlCatalogCategorySavePostdispatch($observer)
    {
        if(Mage::registry('aitoc_catalog_catagory_clear_session_data')) {
            Mage::getSingleton('adminhtml/session')->setCategoryData(array());
        }
    }
    
    public function onAdminhtmlCatalogProductReviewMassDeletePredispatch($observer)
    {
        if (!$this->_getCurrentRole()->isPermissionsEnabled())
        {
            return;
        }

        $reviewIds = $observer->getData('controller_action')->getRequest()->getParam('reviews');
        $notAllowedReviewIds = array();

        foreach ($reviewIds as $id => $reviewId)
        {
            $reviewStoreId = Mage::getModel('review/review')->load($reviewId)->getData('store_id');
            if (!in_array($reviewStoreId, $this->_getCurrentRole()->getAllowedStoreviewIds()))
            {
                unset($reviewIds[$id]);
                $notAllowedReviewIds[] = $reviewId;
            }
        }
        
        if (!empty($notAllowedReviewIds))
        {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->_helper->__('Some review(s) could not be deleted due to insufficent permissions.')
            );
            
            $observer->getData('controller_action')->getRequest()->setParam('reviews', $reviewIds);
        }
    }

    public function updateAttributeValue($storeId, $productId, $data)
    {
		Mage::getSingleton('catalog/product_action')->updateAttributes(array($productId), $data, $storeId);
    }

    public function removeAddNewProductButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product)
        {
            if (!$this->_getCurrentRole()->isPermissionsEnabled())
            {
                return;
            }

            $getCurrentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
            $canCreateProducts = Mage::getModel('aitpermissions/advancedrole')->canCreateProducts($getCurrentUserRole);
            if(!$canCreateProducts)
            {
                $block->removeButton('add_new');
            }

            return;
        }
    }

    public function denyToCreateProduct(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('admin/session');
        $getCurrentUserRole = $session->getUser()->getRole()->getRoleId();
        $canCreateProducts = Mage::getModel('aitpermissions/advancedrole')->canCreateProducts($getCurrentUserRole);
        if(!$canCreateProducts)
        {            
            $controller = Mage::app()->getFrontController();
            $controller->getResponse()
                ->setRedirect(Mage::getModel('adminhtml/url')->getUrl(
                    '*/*/'))
                ->sendResponse();
        }
    }
}
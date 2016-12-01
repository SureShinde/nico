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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogCategoryTree
    extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    public function getCategoryCollection()
    {
        $collection = parent::getCategoryCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $allowedCategoryIds = array();

            foreach ($role->getAllowedCategoryIds() as $allowedCategoryId)
            {
                $category = Mage::getModel('catalog/category')->load($allowedCategoryId);
                $categoryPath = $category->getPath();
                $categoryPathIds = explode('/', $categoryPath);

                $allowedCategoryIds = array_merge($allowedCategoryIds, $categoryPathIds);
            }

            if (!empty($allowedCategoryIds))
            {
                $collection->addIdFilter($allowedCategoryIds);
                $this->setData('category_collection', $collection);
            }
        }

        return $collection;
    }

    public function getMoveUrlPattern()
    {
        return $this->getUrl('*/catalog_category/move', array('store' => ':store'));
    }
}
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
class Aitoc_Aitpermissions_Model_Approvecategory extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/approvecategory', 'id');
    }

    const AIT_CATEGORY_STATUS_AWAITING = 0;
    const AIT_CATEGORY_STATUS_APPROVED = 1;
    const AIT_CATEGORY_STATUS_NOT_APPROVED = 2;

    public function isCategoryApproved($categoryId)
    {        
        $item = $this->getCollection()->loadByCategoryId($categoryId)->getFirstItem();
        if(isset($item))
            return $item->getIsApproved();

        return true;
    }

    public function isCategoryAwaitingApproving($categoryId)
    {        
        $item = $this->getCollection()->loadByCategoryId($categoryId)->getFirstItem();
        if(isset($item) && $item->getIsApproved() == self::AIT_CATEGORY_STATUS_AWAITING)
            return true;

        return false;
    }

    public function approve($categoryId)
    {
        $collection = $this->getCollection()->loadByCategoryId($categoryId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved(self::AIT_CATEGORY_STATUS_APPROVED)->save();
            }
        }
        else
        {
            $this->setCategoryId($categoryId)->setIsApproved(self::AIT_CATEGORY_STATUS_APPROVED)->save();
        }

        return true;
    }

    public function disapprove($categoryId)
    {
        $collection = $this->getCollection()->loadByCategoryId($categoryId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved(self::AIT_CATEGORY_STATUS_NOT_APPROVED)->save();
            }
        }
        else
        {
            $this->setCategoryId($categoryId)->setIsApproved(self::AIT_CATEGORY_STATUS_NOT_APPROVED)->save();
        }

        return true;
    }

    public function add($categoryId)
    {
        $collection = $this->getCollection()->loadByCategoryId($categoryId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved(self::AIT_CATEGORY_STATUS_AWAITING)->save();
            }
        }
        else
        {
            $this->setCategoryId($categoryId)->setIsApproved(self::AIT_CATEGORY_STATUS_AWAITING)->save();
        }

        return true;
    }
}
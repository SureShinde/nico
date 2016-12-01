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
class Aitoc_Aitpermissions_Block_Rewrite_AdminReportProductViewedGrid extends Mage_Adminhtml_Block_Report_Product_Viewed_Grid
{
    /*
    * @return Varien_Object
    */
    public function getFilterData()
    {
        $filter = parent::getFilterData();
        $filter->setStoreIds(
            implode(',', Mage::helper('aitpermissions/access')
                    ->getFilteredStoreIds(
                    $filter->getStoreIds() ? explode(',', $filter->getStoreIds()) : array()
                )
            )
        );
        return $filter;
    }
}
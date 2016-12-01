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
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditTabInventory
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    protected function _toHtml()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->canEditGlobalAttributes() || ($role->getScope() == 'website' && (string)Mage::getConfig()->getModuleConfig('Aitoc_Aitquantitymanager')->active == 'true') || $this->getRequest()->getActionName() == 'new')
        {
            return parent::_toHtml();
        }

        return parent::_toHtml() . '
            <input id="aitpermissions_inventory_manage_stock_default" name="product[stock_data][use_config_manage_stock]" type="hidden" value="1" />
            <script type="text/javascript">
            //<![CDATA[
            if (Prototype.Browser.IE)
            {
                if (window.addEventListener)
                {
                    window.addEventListener("load", disableInventoryInputs, false);
                }
                else
                {
                    window.attachEvent("onload", disableInventoryInputs);
                }
            }
            else
            {
                document.observe("dom:loaded", disableInventoryInputs);
            }

            function disableInventoryInputs()
            {
                var elements = $("table_cataloginventory").select(\'input[type="checkbox"],input[type="text"],select\');
                if (elements.size)
                {
                    elements.each(function(el) {
                        el.disabled = true;
                    });
                }

                if(typeof($("inventory_use_config_manage_stock")) != "undefined");
                {
                    if($("inventory_use_config_manage_stock").checked)
                    {
                        $("aitpermissions_inventory_manage_stock_default").value = 1;
                    }
                    else
                    {
                        $("aitpermissions_inventory_manage_stock_default").value = 0;
                    }
                }
            }
            //]]>
            </script>';
    }
}
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
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererFieldsetElement
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    protected $_disableAlways = array('created_by');

    public function checkFieldDisable()
    {
        $result = parent::checkFieldDisable();

        $role = Mage::getSingleton('aitpermissions/role');

        if(!($this->getElement() && $this->getElement()->getEntityAttribute()) || !$role->isPermissionsEnabled())
        {
            return $result;
        }

        if (in_array($this->getElement()->getEntityAttribute()->getAttributeCode(), $this->_disableAlways))
        {
            $this->_disableElement();
            return $result;
        }

        if ($this->_isNewProduct())
        {
            return $result;
        }

        $attributePermissionArray =  Mage::helper('aitpermissions')->getAttributePermission();

        if(isset($attributePermissionArray[$this->getElement()->getEntityAttribute()->getAttributeId()]))
        {
            if($attributePermissionArray[$this->getElement()->getEntityAttribute()->getAttributeId()] == 0)
            {
                $this->_disableElement();
            }

            return $result;
        }

        if ($this->getElement()->getEntityAttribute()->isScopeGlobal() && !$role->canEditGlobalAttributes())
        {
                $this->_disableElement();
        }
        elseif ($this->getElement()->getEntityAttribute()->isScopeWebsite() && $role->getScope() != 'website')
        {
                $this->_disableElement();
        }


        return $result;
    }

    protected function _disableElement()
    {
        $this->getElement()->setDisabled(true);
        $this->getElement()->setReadonly(true);

        $afterHtml = $this->getElement()->getAfterElementHtml();
        if (false !== strpos($afterHtml, 'type="checkbox"'))
        {
            $afterHtml = str_replace('type="checkbox"', 'type="checkbox" disabled="disabled"', $afterHtml);
            $this->getElement()->setAfterElementHtml($afterHtml);
        }
    }

    protected function _isNewProduct()
    {
        if($this->getRequest()->getActionName() == 'new')
        {
            return true;
        }
        return false;

    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::render($element);
        if ($this->getElement() && 
            $this->getElement()->getEntityAttribute() &&
            $this->getElement()->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() &&
                !$role->canEditGlobalAttributes() &&
                ('msrp' == $this->getElement()->getHtmlId()))
            {
                 $html .= '
                    <script type="text/javascript">
                    //<![CDATA[
                    if (Prototype.Browser.IE)
                    {
                        if (window.addEventListener)
                        {
                            window.addEventListener("load", aitpermissions_disable_msrp, false);
                        }
                        else
                        {
                            window.attachEvent("onload", aitpermissions_disable_msrp);
                        }
                    }
                    else
                    {
                        document.observe("dom:loaded", aitpermissions_disable_msrp);
                    }

                    function aitpermissions_disable_msrp()
                    {
                        ["click", "focus", "change"].each(function(evt){
                            var msrp = $("msrp");
                            if (msrp && !msrp.disabled)
                            {
                                Event.observe(msrp, evt, function(el) {
                                    el.disabled = true;
                                }.curry(msrp));
                            }
                        });
                    }
                    //]]>
                    </script>';
            }

            if (!$role->canEditGlobalAttributes())
            {
                $html = str_replace(
                    '<script type="text/javascript">toggleValueElements(',
                    '<script type="text/javascript">//toggleValueElements(',
                    $html
                );
            }
        }
        
        return $html;
    }
}
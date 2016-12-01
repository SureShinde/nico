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

class MageParts_Base_Block_Adminhtml_System_Config_Form_Field_Urltable extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var MageParts_Base_Block_Adminhtml_System_Config_Form_Field_Urltable_Type
     */
    protected $_typeRenderer;

    public function __construct()
    {
        $this->addColumn('url', array(
            'label' => Mage::helper('mageparts_base')->__('URL'),
            'style' => 'width:229px',
        ));

        $this->addColumn('type', array(
            'label' => Mage::helper('mageparts_base')->__('Type'),
            'renderer' => $this->_getTypeRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('mageparts_base')->__('Add URL');
        parent::__construct();
    }

    /**
     * Retrieve url type column renderer.
     *
     * @return MageParts_Base_Block_Adminhtml_System_Config_Form_Field_Urltable_Type
     */
    protected function _getTypeRenderer()
    {
        if (!$this->_typeRenderer) {
            $this->_typeRenderer = Mage::getSingleton('core/layout')->createBlock(
                'mageparts_base/adminhtml_system_config_form_field_urltable_type', '',
                array('is_render_to_js_template' => true)
            );
            $this->_typeRenderer->setClass('url_type_select');
            $this->_typeRenderer->setExtraParams('style="width:175px"');
        }
        return $this->_typeRenderer;
    }

    /**
     * Prepare existing row data object.
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTypeRenderer()->calcOptionHash($row->getData('type')),
            'selected="selected"'
        );
    }
    
}

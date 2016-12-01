<?php
/**
 * EmJa Interactive, LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.emjainteractive.com/LICENSE.txt
 *
 * @category   EmJaInteractive
 * @package    EmJaInteractive_ShippingOption
 * @copyright  Copyright (c) 2010 EmJa Interactive, LLC. (http://www.emjainteractive.com)
 * @license    http://www.emjainteractive.com/LICENSE.txt
 */

class Emjainteractive_ShippingOption_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('optionGrid');
        $this->setDefaultSort('label');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
    	if( !$this->getCollection() ) {
            $collection = Mage::getModel('emjainteractive_shippingoption/option')->getResourceCollection();
            $this->setCollection($collection);
    	}
        return $this->getCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('option_id', array(
            'header'    => Mage::helper('emjainteractive_shippingoption')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'option_id',
        ));

        $this->addColumn('label', array(
            'header'    => Mage::helper('emjainteractive_shippingoption')->__('Label'),
            'align'     => 'left',
            'index'     => 'label',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('emjainteractive_shippingoption')->__('Code'),
            'align'     => 'left',
            'index'     => 'code',
        ));
        
        $this->addColumn('type', array(
            'header'    => Mage::helper('emjainteractive_shippingoption')->__('Type'),
            'align'     => 'left',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
                Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_TEXT => Mage::helper('emjainteractive_shippingoption')->__('Text Input'),
                Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_TEXTAREA => Mage::helper('emjainteractive_shippingoption')->__('Text Area'),
                Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_CHECKBOX => Mage::helper('emjainteractive_shippingoption')->__('Checkbox'),
                Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_RADIO => Mage::helper('emjainteractive_shippingoption')->__('Radio'),
                Emjainteractive_ShippingOption_Model_Option::OPTION_TYPE_SELECT => Mage::helper('emjainteractive_shippingoption')->__('Select'),
            ),
        ));
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('emjainteractive_shippingoption')->__('Sort Order'),
            'align'     => 'right',
            'type'      => 'number',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('option_id' => $row->getId()));
    }
}
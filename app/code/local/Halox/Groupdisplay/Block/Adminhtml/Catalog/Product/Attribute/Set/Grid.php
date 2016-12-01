<?php
/**
 * add two new columns named Edit Attribute Set and Edit Group under manage attribute set grid  
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Block_Adminhtml_Catalog_Product_Attribute_Set_Grid
    extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Grid
{
    
    /**
     * it prepares columns Edit Attribute set and Edit Group
     * @return 
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

            $this->addColumn('edit_attribute_set', array(
            'header'    => '',
            'width'     => '125px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('halox_groupdisplay')->__('Edit Attribute Set'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id'
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addColumn('edit_attribute_set_display', array(
            'header'    => '',
            'width'     => '125px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('halox_groupdisplay')->__('Edit Group'),
                    'url'     => array('base' => 'adminhtml/catalog_product_set_group/edit'),
                    'field'   => 'id'
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
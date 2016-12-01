<?php
/**
 * Group display settings edit form
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Block_Adminhtml_Catalog_Product_Attribute_Group_Form_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'halox_groupdisplay';
        $this->_controller = 'adminhtml_catalog_product_attribute_group_form';
        $referralUrl = $_SERVER['HTTP_REFERER'];
       
        $this->_removeButton('back');
        $this->addButton('back', array(
            'label'     => Mage::helper('halox_groupdisplay')->__('Back'),
            'onclick'   => 'setLocation(\'' . $referralUrl . '\')',
            'class'     => 'back',
        ), -1);
        
        $this->_removeButton('delete');
         
         
        $this->_updateButton('save', 'label', Mage::helper('halox_groupdisplay')->__('Save'));

    }

    /**
     * it returns header to display on edit display setting form
     * @return string
     */
    
    public function getHeaderText()
    {
        return Mage::helper('halox_groupdisplay')->__("Edit Group Frontend display setting for Attribute Set '%s'", $this->_getAttributeSet()->getAttributeSetName());
    }

    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTree()
    {
        $items = array();
        $setId = $this->_getSetId();
        
        $group_list = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load()
            ->getData();
        
        return $group_list;
    }

    /**
     * returns the current attribute set
     *
     * @return attribute set object
     */
    protected function _getAttributeSet()
    {
        return Mage::registry('current_attribute_set');
    }
    
    /**
     * getback url
     *
     * @return url
     */
    
     public function getBackUrl()
    {
        return $this->getUrl('*/*/*/');
    }
   
}
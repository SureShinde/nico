<?php

/**
 * Group display Source model
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Model_System_Config_Source_Template {

    const TAB_VIEW_CODE = 1;
    const LIST_VIEW_CODE = 2;

    /**
     * get the options for the group display settings under configurations/Halox/attribute group settings in admin
     * return type model
     */
    public function toOptionArray() {
        return array(
            array('value' => static::LIST_VIEW_CODE, 'label' => Mage::helper('halox_groupdisplay')->__('List view')),
            array('value' => static::TAB_VIEW_CODE, 'label' => Mage::helper('halox_groupdisplay')->__('Tab view')),
        );
    }

}

<?php

/**
 * Group display Mapping model
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Model_Resource_Mapping extends Mage_Core_Model_Resource_Db_Abstract {
    /*
     * get the mappings on the bases of attribute set id
     * 
     */

    protected function _construct() {
        $this->_init('halox_groupdisplay/mapping', 'id');
    }

}

<?php
/**
 * Group display collection
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Model_Resource_Mapping_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	

	public function _construct()
    {
        parent::_construct();
        $this->_init('halox_groupdisplay/mapping');
    }
}
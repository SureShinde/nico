<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author Administrator
 */
class Sunovisio_CustomerFlag_Model_Mysql4_Flag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('customerflag/flag');
    }
}

?>

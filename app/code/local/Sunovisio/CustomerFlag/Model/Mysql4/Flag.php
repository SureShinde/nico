<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Flag
 *
 * @author Administrator
 */
class Sunovisio_CustomerFlag_Model_Mysql4_Flag extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {    
        $this->_init('customerflag/flag', 'id');
    }
}

?>

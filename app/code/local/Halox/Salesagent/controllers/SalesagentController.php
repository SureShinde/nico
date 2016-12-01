<?php
/**
 * Halox_Salesagent extension
 * 
 * @category       Halox
 * @package        Halox_Salesagent
 * @copyright      Copyright (c) 2016
 */
/**
 * Salesagent admin controller
 *
 * @category    Halox
 * @package     Halox_Salesagent
 */

class Halox_Salesagent_SalesagentController
    extends Mage_Core_Controller_Front_Action {
	public function indexAction(){
	    $this->loadLayout(); 
        $this->renderLayout();
	}
	
}
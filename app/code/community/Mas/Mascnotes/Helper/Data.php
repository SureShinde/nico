<?php 
/**
 * Mas_Mascnotes extension by Makarovsoft.com
 * 
 * @category   	Mas
 * @package		Mas_Mascnotes
 * @copyright  	Copyright (c) 2014
 * @license		http://makarovsoft.com/license.txt
 * @author		makarovsoft.com
 */
/**
 * Mascnotes default helper
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getAdmins()
	{
		$admins = Mage::getModel('admin/user')->getCollection();
		$return = array();
		foreach($admins as $admin){
			$return[$admin->getUserId()] = $admin->getFirstname() . ' ' . $admin->getLastname(); 
		}
		return $return;
	}
}
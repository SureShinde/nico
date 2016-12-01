<?php
/**
 *
 * @category   MW
 * @package    HelpDesk
 * @author     khanhpn, Mage-World Company <khanhpnk@gmail.com>
 */
class MW_HelpDesk_Model_Config_Source_Priority extends Varien_Object
{
    const PRIORITY_NORMAL		= 1;
    const PRIORITY_HIGHT		= 2;
    const PRIORITY_EMERGENCY	= 3;

    static public function getOptionArray()
    {
        return array(
            self::PRIORITY_NORMAL    => Mage::helper('helpdesk')->__('Normal'),
            self::PRIORITY_HIGHT 	 => Mage::helper('helpdesk')->__('High'),
            self::PRIORITY_EMERGENCY => Mage::helper('helpdesk')->__('Urgent')
        );
    }
	
    /*
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
	*/
	
	static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	if($type != ''){
    		if($type > 0 && $type <= sizeof($options)){
    			return $options[$type];
    		}
    	}
    	return '';
    }
	
	public function getOptionArrPriority()
    {	
      	$dur = array();
      	$dur[] = array('value' => '', 'label'=>Mage::helper('helpdesk')->__('-- Please select priority --'));
      	$dur[] = array('value' => 1, 'label'=>Mage::helper('helpdesk')->__('Normal'));
      	$dur[] = array('value' => 2, 'label'=>Mage::helper('helpdesk')->__('High'));
      	$dur[] = array('value' => 3, 'label'=>Mage::helper('helpdesk')->__('Urgent'));
        return $dur;
    }
    
	public function toOptionArray()
    {	
      	$dur = array();
      	$dur[] = array('value' => '', 'label'=>'');
      	$dur[] = array('value' => 1, 'label'=>Mage::helper('helpdesk')->__('Normal'));
      	$dur[] = array('value' => 2, 'label'=>Mage::helper('helpdesk')->__('High'));
      	$dur[] = array('value' => 3, 'label'=>Mage::helper('helpdesk')->__('Urgent'));
        return $dur;
    }
    
    public function getOptionArrCateResponse()
    {	
    	$categories = unserialize(Mage::getStoreConfig('helpdesk/config/category_response'));
      	$dur = array();
      	$dur[] = array('value' => '', 'label'=>Mage::helper('helpdesk')->__('-- Please Select Folder --'));
      	foreach ($categories as $cate){
      		$dur[] = array('value' => $cate['id_category'], 'label'=>$cate['name_category']);
      	}
        return $dur;
    }
    
	public function toOptionArrCateResponse()
    {	
    	$categories = unserialize(Mage::getStoreConfig('helpdesk/config/category_response'));
      	$dur = array();
      	foreach ($categories as $cate){
      		$dur[] = array('value' => $cate['id_category'], 'label'=>$cate['name_category']);
      	}
        return $dur;
    }	
}
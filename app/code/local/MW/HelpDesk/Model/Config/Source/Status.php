<?php
/**
 *
 * @category   MW
 * @package    HelpDesk
 * @author     khanhpn, Mage-World Company <khanhpnk@gmail.com>
 */
class MW_HelpDesk_Model_Config_Source_Status extends Varien_Object
{
	const STATUS_OPEN	= 1;
    const STATUS_PROCESSING = 2;
    const STATUS_CLOSED		= 3;
	const STATUS_NEW		= 4;
	const STATUS_ONHOLD		= 5;
	const STATUS_ACTIVE		= 100;

	const STATUS_DEFAULT	= 1;
    const STATUS_YES		= 2;
    const STATUS_NO			= 3;
	
    static public function getOptionArray()
    {
        return array(
            self::STATUS_OPEN   	=> Mage::helper('helpdesk')->__('Open'),
            self::STATUS_PROCESSING => Mage::helper('helpdesk')->__('Pending Customer Response'),
            self::STATUS_CLOSED  	=> Mage::helper('helpdesk')->__('Closed'),
			self::STATUS_NEW  		=> Mage::helper('helpdesk')->__('New'),
			self::STATUS_ONHOLD  	=> Mage::helper('helpdesk')->__('On Hold')
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
	
	public function getOptionArrStatus()
    {	
      	$dur = array();
      	$dur[] = array('value' => '', 'label'=>Mage::helper('helpdesk')->__('-- Please select status --'));
      	$dur[] = array('value' => 1, 'label'=>Mage::helper('helpdesk')->__('Open'));
      	$dur[] = array('value' => 2, 'label'=>Mage::helper('helpdesk')->__('Pending Customer Response'));
      	$dur[] = array('value' => 3, 'label'=>Mage::helper('helpdesk')->__('Closed'));
		$dur[] = array('value' => 4, 'label'=>Mage::helper('helpdesk')->__('New'));
		$dur[] = array('value' => 5, 'label'=>Mage::helper('helpdesk')->__('On Hold'));
        return $dur;
    }
    
	static public function getOptionArrayGrid()
    {
        return array(
        	self::STATUS_ACTIVE  	=> Mage::helper('helpdesk')->__('Active (New/Open)'),
			self::STATUS_NEW  		=> Mage::helper('helpdesk')->__('New'),
            self::STATUS_OPEN   	=> Mage::helper('helpdesk')->__('Open'),
            self::STATUS_PROCESSING => Mage::helper('helpdesk')->__('Pending Customer Response'),
			self::STATUS_ONHOLD  	=> Mage::helper('helpdesk')->__('On Hold'),
            self::STATUS_CLOSED  	=> Mage::helper('helpdesk')->__('Closed')
        );
    }
	
	public function toOptionArray()
    {	
      	$dur = array();
      	$dur[] = array('value' => '', 'label'=>'');
      	$dur[] = array('value' => 1, 'label'=>Mage::helper('helpdesk')->__('Open'));
      	$dur[] = array('value' => 2, 'label'=>Mage::helper('helpdesk')->__('Pending Customer Response'));
      	$dur[] = array('value' => 3, 'label'=>Mage::helper('helpdesk')->__('Closed'));
		$dur[] = array('value' => 4, 'label'=>Mage::helper('helpdesk')->__('New'));
		$dur[] = array('value' => 5, 'label'=>Mage::helper('helpdesk')->__('On Hold'));
        return $dur;
    }
	
	public function toOptionEmailNotify()
    {	
      	$dur = array();
      	$dur[] = array('value' => self::STATUS_DEFAULT, 'label'=>Mage::helper('helpdesk')->__('Default'));
      	$dur[] = array('value' => self::STATUS_YES, 'label'=>Mage::helper('helpdesk')->__('Yes'));
      	$dur[] = array('value' => self::STATUS_NO, 'label'=>Mage::helper('helpdesk')->__('No'));
        return $dur;
    }
}
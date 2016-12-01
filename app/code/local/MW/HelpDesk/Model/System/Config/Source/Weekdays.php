<?php
/**
 *
 * @category   MW
 * @package    HelpDesk
 * @author     khanhpn, Mage-World Company <khanhpnk@gmail.com>
 */
class MW_Helpdesk_Model_System_Config_Source_Weekdays
{

    /**
     * Options getter
     *
     * @return array
     */
	/*
    public function toOptionArray()
    {	
      	$weekdays = array();
      	$weekdays[] = array('value' => 'Sunday', 'label'=>'Sunday');
      	$weekdays[] = array('value' => 'Monday', 'label'=>'Monday');
      	$weekdays[] = array('value' => 'Tuesday', 'label'=>'Tuesday');
     	$weekdays[] = array('value' => 'Wednesday', 'label'=>'Wednesday');
 		$weekdays[] = array('value' => 'Thursday', 'label'=>'Thursday');
      	$weekdays[] = array('value' => 'Friday', 'label'=>'Friday');
      	$weekdays[] = array('value' => 'Saturday', 'label'=>'Saturday');	
        return $weekdays;
    }
	*/

	public function toOptionArray()
    {	
    	return Mage::app()->getLocale()->getOptionWeekdays();
    }
}
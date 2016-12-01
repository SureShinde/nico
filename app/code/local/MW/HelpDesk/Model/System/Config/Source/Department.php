<?php
/**
 *
 * @category   MW
 * @package    HelpDesk
 * @author     khanhpn, Mage-World Company <khanhpnk@gmail.com>
 */
class MW_Helpdesk_Model_System_Config_Source_Department
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {	
      	$departmentsArr = array();
      	$departmentsArr[''] = Mage::helper('helpdesk')->__('-- No Settings --');
		$departments = Mage::getResourceModel('helpdesk/department_collection');
		foreach($departments as $department) {
			$departmentsArr[$department->getId()] = $department->getName();
		}
		
        return $departmentsArr;
    }

}
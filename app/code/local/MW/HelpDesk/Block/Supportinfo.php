<?php
class MW_HelpDesk_Block_Supportinfo extends Mage_Core_Block_Template{
    protected $workingTimes = null;
    protected $breakingTimes = null;
    
    //returned array type from PHPs getdate()
    private $now = null;
    
    protected $dayOff= null;
    
    protected $dayoff = null;
    protected $isCheckDayOff = false;


    public function __construct() {
        parent::__construct();
        $this->setTemplate('mw_helpdesk/supportinfo.phtml');
    }
    
    public function displayWorkingDays(){
        $weekends = Mage::getStoreConfig('helpdesk/support_time/weekend');
        $weekend = explode(',', $weekends);
        $helper = Mage::helper('helpdesk/data');
        $startWeekend = $helper->convertDateByLocate($weekend[0]);
        $endWeekend = $helper->convertDateByLocate($weekend[count($weekend) - 1]);
        
        if($weekends == '' || $weekends == '0,1,2,3,4,5,6'){
        	$startWeekend = Mage::helper('helpdesk')->__("Monday");
        	$endWeekend = Mage::helper('helpdesk')->__("Sunday");
        }
        
        return $startWeekend.' - '.$endWeekend;
    }
    
    public function getNow(){
        if(empty($this->now)){
            $this->now = getdate(Mage::getSingleton('core/date')->timestamp());
        }
        
        return $this->now;
    }
    
    public function getWorkingTimes(){
        if(empty($this->workingTimes)){
            $this->workingTimes = $this->prepareTime(Mage::getStoreConfig('helpdesk/support_time/working_time'));
        }
        return $this->workingTimes;
    }
    
    public function getBreakingTimes(){
        if(empty($this->breakingTimes)){
            $this->breakingTimes = $this->prepareTime(Mage::getStoreConfig('helpdesk/support_time/break_time'));
        }
        return $this->breakingTimes;
    }
    
    public function prepareTime($strTime){
        $now = $this->getNow();
        preg_match_all('/([0-9]{2}):([0-9]{2})/', $strTime, $times);
        $timeArray[0] = mktime(intval($times[1][0]), intval($times[2][0]), 0, $now['mon'], $now['mday'], $now['year']);
        $timeArray[1] = mktime(intval($times[1][1]), intval($times[2][1]), 0, $now['mon'], $now['mday'], $now['year']);
        return $timeArray;
    }
    
    public function displayWorkingTimes(){
        $workingTimes = $this->getWorkingTimes();
        $brekingTimes = $this->getBreakingTimes();
        
        return date('g:i A', $workingTimes[0]).' - '.date('g:i A', $brekingTimes[0]).' | '.date('g:i A', $brekingTimes[1]).' - '.date('g:i A', $workingTimes[1]);
    }
    
    public function displayNow(){ 	
   		//$date=Mage::helper('helpdesk')->locale_time_format(Mage::getSingleton('core/date')->timestamp(),Mage_Core_Model_Locale::FORMAT_TYPE_FULL,"H:i:s");
   		$date=Mage::helper('helpdesk')->locale_time_format(Mage::getSingleton('core/date')->timestamp(),Mage_Core_Model_Locale::FORMAT_TYPE_FULL);		
    	return $date;
        /*
        $now = $this->getNow();
        return date('l, F jS, Y, h:i A', $now[0]);
        */
    }
    
    public function getDayOff(){
        if(!$this->isCheckDayOff){
            $this->checkDayOff();
        }
        return $this->dayOff;
    }
    
    public function checkStatus(){
        if(!$this->isCheckDayOff){
            $this->checkDayOff();
        }
        if(empty($this->dayOff)){
            $breakingTimes = $this->getBreakingTimes();
            $workingTimes = $this->getWorkingTimes();
            $now = $this->getNow();
            if((($now[0]>= $workingTimes[0])&&($now[0] <= $breakingTimes[0])) 
                    || (($now[0]>= $breakingTimes[1])&&($now[0] <= $workingTimes[1]))){
                return true;
            }
        }
        return false;
    }
    
    public function checkDayOff(){
        $this->isCheckDayOff = true;
        $day_offs = unserialize(Mage::getStoreConfig('helpdesk/support_time/day_off'));
        if(!empty($day_offs)){
            $now = $this->getNow();
            foreach ($day_offs as $day_off){
                preg_match_all('/(\d+)\/(\d+)/', $day_off['day_value'], $dayArray, PREG_SET_ORDER);
                $startDate = mktime(0, 0, 0, intval($dayArray[0][2]), intval($dayArray[0][1]), $now['year']);
                $nowDate = mktime(0, 0, 0, $now['mon'], $now['mday'], $now['year']);
                if($startDate == $nowDate){
                    $this->dayOff = $day_off['day_title'];
                    break;
                }else if(($startDate < $nowDate)&&(isset($dayArray[1]))){
                    $endDate = mktime(0, 0, 0, intval($dayArray[1][2]), intval($dayArray[1][1]), $now['year']);
                    if($endDate < $startDate){
                        $endDate = mktime(0, 0, 0, intval($dayArray[1][2]), intval($dayArray[1][1]), intval($now['year'])+1);
                    }
                    if($nowDate <= $endDate){
                        $this->dayOff = $day_off['day_title'];
                        break;
                    }
                }
            }
        }
    }
    
    public function displayGmt(){
        $Est = Mage::getModel('core/date')->getGmtOffset() / 3600;
        if ($Est > 0) {
        $Est = '+' . $Est;
        }
        return '( GMT' . $Est . ' )' ;
    }
}
?>

<?php
class MW_HelpDesk_Model_Spam extends Mage_Core_Model_Abstract {
    private $spamList = null;
    
    public function _construct() {
        parent::_construct();
        $this->_init('helpdesk/spam');
    }
    
    public function checkSpam($email){
        
        if(is_null($this->spamList)){
            $spams = $this->getCollection();
            $spamList = array();
            foreach($spams as $spam){
                $spamList[$spam->getEmail()] = '1';
            }
            $this->spamList = $spamList;
        }
        if(isset($this->spamList[$email])){
            return true;
        }else{
            return false;
        }
    }
}
?>

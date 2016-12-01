<?php
class MW_HelpDesk_GatewayController extends Mage_Core_Controller_Front_Action
{
     protected function _isAllowed()
    {
        return true;
    }
	public function testAction()
    {
    	$request = $this->getRequest()->getParams();
		//var_dump($request);die();
		$dataConnect = array( 	'host' => $request['host'],
					  			'user' => $request['email'],
					  			'password' => $request['password'],
		);
		
		if($request['ssl'] == 1) {
			$dataConnect['ssl'] = 'SSL';
		}
    	if($request['port'] != '') {
			$dataConnect['port'] = $request['port'];
		}
        if($request['login'] != '') {
			$dataConnect['user'] = $request['login'];
		}
		
	    try {
	    	if($request['type'] == 1) {
				$mail = new Zend_Mail_Storage_Imap($dataConnect);
	    	} else {
	    		$mail = new Zend_Mail_Storage_Pop3($dataConnect);
	    	}
			echo  'The Gateway is connected succesfully.';
        } catch (Exception $e) {
			echo 'The Gateway is connected failed.';
        }
    }
    
	public function testCronAction()
    {
    	echo 'run Cron ...<br />';
		Mage::getModel('helpdesk/email')->runCron();
    }
    
	public function refeshAction()
    {
    	$flag = Mage::getModel('helpdesk/email')->runCron();
    	
    	if($flag) {
    		Mage::getSingleton('adminhtml/session')->addSuccess("System has been refreshed.");
    	} else {
    		Mage::getSingleton('adminhtml/session')->addSuccess("System has been refreshed.");
    	}
    }
     
}
<?php

class MW_HelpDesk_AccountController extends Mage_Core_Controller_Front_Action {
    private $redirectUrl = null;
    
    private function _getDepartmentData() {
        return Mage::getModel('helpdesk/department')->getCollection()
                        ->addFieldToFilter('active', array('eq' => 1));
    }
     protected function _isAllowed()
    {
        return true;
    }
    private function _getDepartmentDescriptionData($id) {
        $collection = Mage::getModel('helpdesk/department')->getCollection()
                ->addFieldToFilter('active', array('eq' => 1))
                ->addFieldToFilter('department_id', array('eq' => $id));
        foreach ($collection as $department) {
            return $department->getDescription();
        }
    }

    private function _getPriorityData() {
        return Mage::getModel('helpdesk/config_source_priority')->getOptionArray();
    }

    public function preDispatch() {
        parent::preDispatch();

        if (!Mage::getStoreConfig('helpdesk/config/enabled')) {
            $this->norouteAction();
        }

//    	if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
//    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
//        }
    }

    public function closeAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $ticket = Mage::getModel('helpdesk/ticket')->load($data['ticket_id']);
                $ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED);
                $ticket->save();
                Mage::getSingleton('customer/session')->addSuccess($this->__('The ticket has been closed successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to close'));
            }
            $this->_redirect('helpdesk/account/history/id/' . $data['ticket_id']);
        }
    }

    public function submitAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Helpdesk'));
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function showAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Helpdesk'));
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
	
	public function downloadAction(){
    	$path =  $this->getRequest()->getParam('file');
    	$fullPath = 'media'. DS . 'ticket' . DS . $path;
		//echo $fullPath;die;
		//echo $license;die;
		$pieces = explode("/", $fullPath);
		$license = $pieces[sizeof($pieces)-1];
		$file_names = explode("\\", $license);
		//echo 'license: ' . $license;die;
		if ($fd = fopen ($fullPath, "r")) {
			//echo"sdfdf";die;
			$fsize = filesize($fullPath);
			//echo $fsize.'aaaaaaaaaaaa';die;
			$path_parts = pathinfo($fullPath);
			$ext = strtolower($path_parts["extension"]);
			//echo $ext;die();
			switch ($ext) {
				case "pdf":
				header("Content-type: application/pdf"); // add here more headers for diff. extensions
				header("Content-Disposition: attachment; filename=\"".$file_names[sizeof($file_names)-1]."\""); // use 'attachment' to force a download
				break;
				default;
				header("Content-type: application/octet-stream");
				header("Content-Disposition:attachment; filename=\"".$file_names[sizeof($file_names)-1]."\"");
			}
			header("Content-length: $fsize");
			header("Cache-control: private"); //use this to open files directly
			while(!feof($fd)) {
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
		}
		fclose ($fd);
		exit;
    }

    public function historyAction() {
        // dam bao chi xem dc ticketid cua minh
        if ($id = $this->getRequest()->getParam('id')) {
            $email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
            $ticket = Mage::getModel('helpdesk/ticket')->load($id);
            if ($ticket->getSender() != $email) {
                $this->_redirect('helpdesk/account/show/');
                //$this->norouteAction();
            }
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Helpdesk'));
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function sentAction() {
        if ($data = $this->getRequest()->getPost()) {
            //echo "<pre>";var_dump($data);die();      
            try {
                $ticket = Mage::getModel('helpdesk/ticket');
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $data['name'] = Mage::getSingleton('customer/session')->getCustomer()->getFirstname() . ' '
                            . Mage::getSingleton('customer/session')->getCustomer()->getLastname();
                    $data['sender'] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
                }

                if (Mage::getSingleton('helpdesk/spam')->checkSpam($data['sender'])) {
                    Mage::getSingleton('customer/session')->addError($this->__("Unable submit ticket"));         
                    $this->_redirect($this->getRedirectUrl());
                    return;
                }
				
				if ($data['content']=="") {
                    Mage::getSingleton('customer/session')->addError($this->__("You must enter message"));         
                    $this->_redirect($this->getRedirectUrl());
                    return;
                }
				
                $file_attachment = Mage::helper('helpdesk')->processMultiUpload();
                if($file_attachment){
                    $data['file_attachment'] = $file_attachment;
                }
				
				$data['store_view'] = Mage::app()->getStore()->getStoreId();
                $error = '';
                /* ftp_form */
				$_ftp_str = "";
                if($data['hd_ftpserver']!="")
                	$_ftp_str = '<br />' .'<b>FTP Server: </b>' . $data['hd_ftpserver'] . '<br />';
                if($data['hd_ftpuser']!="")
                	$_ftp_str .= '<b>FTP User: </b>' . $data['hd_ftpuser'] . '<br />';
                if($data['hd_ftppass']!="")
                	$_ftp_str .= '<b>Password: </b>' . $data['hd_ftppass'] . '<br />';
                if($data['hd_ftp_backend_url']!="")
                	$_ftp_str .= '<b>Magento Backend URL: </b>' . $data['hd_ftp_backend_url'] . '<br />';
                if($data['hd_ftp_backend_admin']!="")
                	$_ftp_str .= '<b>Magento Admin: </b>' . $data['hd_ftp_backend_admin'] . '<br />';
                if($data['hd_ftp_backend_pass']!="")
                	$_ftp_str .= '<b>Password: </b>' . $data['hd_ftp_backend_pass'];
                
                if($_ftp_str != "") $data['content'] = 	$data['content'].$_ftp_str;
				/*
                $data['content'] = 	$data['content'] . '<br />' .
					'<b>FTP Server: </b>' . $data['hd_ftpserver'] . '<br />' .
					'<b>FTP User: </b>' . $data['hd_ftpuser'] . '<br />' .
					'<b>Password: </b>' . $data['hd_ftppass'] . '<br />' .
					'<b>Magento Backend URL: </b>' . $data['hd_ftp_backend_url'] . '<br />' .
					'<b>Magento Admin: </b>' . $data['hd_ftp_backend_admin'] . '<br />' .
					'<b>Password: </b>' . $data['hd_ftp_backend_pass'];
				*/
                $error = $ticket->saveTicket($data);
				
				//add here: status new
				$ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW)
						//->setStoreView(Mage::app()->getStore()->getStoreId())
						->setTicketId($ticket->getId());					
				
				$ticket->save();
				
				//*** process for email ebay
                $helper = Mage::helper('helpdesk/data');
                $ticketCollections = Mage::getModel('helpdesk/ticket')->getCollection()->setOrder('ticket_id', 'DESC');
				foreach ($ticketCollections as $value) {
					if($helper->isEbayMail($value->getSender())>0){
						//save item id
						$str = $value->getContent();
						
						$item_id = trim(str_replace("&nbsp;","",$helper->getItemIdFromEmailEbay($str)));
						$item_id = trim(str_replace("<br />","",$item_id));
						$buyer = trim(str_replace("&nbsp;","",$helper->getBuyerFromEmailEbay($str)));
						//$buyer = trim(str_replace("<br />","",$buyer));

						$regex_pattern2 = "/(\d+)/";
						preg_match($regex_pattern2, $item_id, $matches2);
						$item_id = $matches2[0];
						
						if(strpos($buyer, " ")>0){
							$regex_pattern3 = "/(\w+)(\s)/";
							preg_match($regex_pattern3, $buyer, $matches3);
							$buyer = trim($matches3[0]);
						}
						$email_ref_id = trim($helper->getEmailRefIdFromEmailEbay($str));

						$ticket->setItemId($item_id)->setTicketId($value->getId());          
						$ticket->save();  
						$ticket->setBuyerUsername($buyer)->setTicketId($value->getId());   
						$ticket->save();         
						$ticket->setEmailRefId($email_ref_id)->setTicketId($value->getId());                   		    	
		                $ticket->save();
					}

					break;
				}
				
				/* -------------------- check condition rule -------------------- */
                $model_ticket_rules = Mage::getModel('helpdesk/rules');
                //when ticket created from fronend
				$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('created',$ticket->getId(), 2);
				//echo $list_ruleids;die;
				/* --------------------------------------------------------------- */
				
				/* save ticket log */
				Mage::getModel('helpdesk/ticketlog')->saveEvents2Logs($ticket->getId(), Mage::helper('helpdesk')->__('Creating New Ticket'));
				/*	end save log	*/
				
                if ($error != '') {
                    Mage::getSingleton('customer/session')->addError($this->__($error));
                    $this->_redirect($this->getRedirectUrl());
                    return;
                }
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket was submitted and will be responded to as soon as possible. Thank you for contacting us'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
                $this->_redirect($this->getRedirectUrl());
                return;
            }
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_redirect('helpdesk/account/show/');
            }else{
                $this->_redirect($this->getRedirectUrl());
            }
        }
    }
    
    private function getRedirectUrl(){
        if(is_null($this->redirectUrl)){
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->redirectUrl = 'helpdesk/account/submit';
            }else{
                $this->redirectUrl = 'contacts';
            }
        }
        return $this->redirectUrl;
    }

    public function replyAction() {
        if ($data = $this->getRequest()->getPost()) {
            //echo "<pre>";var_dump($data);die();
            /* support attach 1 file */
//            if (isset($_FILES["file_attachment"]["name"]) && $_FILES["file_attachment"]["name"] != '') {
//                if (Mage::getStoreConfig('helpdesk/client_config/max_upload') != ''
//                        && Mage::getStoreConfig('helpdesk/client_config/max_upload') != 0) {
//                    $size = $_FILES["file_attachment"]['size'] / 1024 / 1024;
//                    $maxUpload = Mage::getStoreConfig('helpdesk/client_config/max_upload');
//                    if ($maxUpload < $size) {
//                        Mage::getSingleton('customer/session')->addError("Max Upload File Size is {$maxUpload} (Mb)");
//                        $this->_redirect('helpdesk/account/history/id/' . $data['ticket_id']);
//                        return;
//                    }
//                }
//
//                try {
//                    //this way the name is saved in DB
//                    $space = array("\r\n", "\n", "\r", " ");
//					$str =  preg_replace('/[^a-z.A-Z0-9]/', "_", $_FILES["file_attachment"]["name"]);
//					$str = preg_replace('/\_\_+/', '_', $str);
//                    //$data['file_attachment'] = $str;
//                    /* Starting upload */
//                    $uploader = new Varien_File_Uploader('file_attachment');
//                    // Any extention would work
//                    //$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
//                    $uploader->setAllowRenameFiles(false);
//                    // Set the file upload mode 
//                    // false -> get the file directly in the specified folder
//                    // true -> get the file in the product like folders 
//                    //	(file.jpg will go in something like /media/f/i/file.jpg)
//                    $uploader->setFilesDispersion(false);
//                    // We set media as the upload dir
//                    $path = Mage::getBaseDir('media') . DS . 'ticket' . DS;
//					/*		insert_here: process when name file attachment		*/
//					$at = "$path".$str."";
//					if(file_exists($at))
//					{
//						$duplicate_filename = TRUE;
//						$i=0;
//						while ($duplicate_filename)
//						{
//							$filename_data = explode(".", $str);
//							$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
//							$str = $new_filename;
//							$at = "$path".$str."";
//							if(file_exists($at))
//							{
//								$i++;
//							}
//							else
//							{
//								$duplicate_filename = FALSE;
//							}
//						}
//					}
//					$data['file_attachment'] = $str;
//                    $uploader->save($path, $data['file_attachment']);
//                } catch (Exception $e) {
//                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to upload file'));
//                    $this->_redirect('helpdesk/account/history/id/' . $data['ticket_id']);
//                    return;
//                }
//            }

        	/* support attach multi file(s) */
        	$file_attachment = Mage::helper('helpdesk')->processMultiUpload();
            if($file_attachment){
            	$data['file_attachment'] = $file_attachment;
           	}
        	
            try {
                $history = Mage::getModel('helpdesk/history');
                $history->saveHistory($data);

				/* -------------------- check condition rule -------------------- */
                $model_ticket_rules = Mage::getModel('helpdesk/rules');
                //when ticket updated from fronend 
				$list_ruleids = $model_ticket_rules->doActionWithTicketWhen('updated', $data['ticket_id'], 2);
				
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your ticket was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Unable to find ticket to save'));
            }
            $this->_redirect('helpdesk/account/history/id/' . $data['ticket_id']);
        }
    }

    public function renderDescriptionAction() {
        echo '<b>Description:</b><br>'
        . nl2br($this->_getDepartmentDescriptionData($this->getRequest()->getParam('id')));
    }

    public function renderFormAction() {       
        $block = $this->getLayout()->createBlock('helpdesk/ticketsubmit');
        echo $block->toHtml();
        //$this->getResponse()->setBody($block->_toHtml());
        // enable display department on frontend
        
//        if (isset($_GET['id'])) {
//            $collection = Mage::getModel('helpdesk/department')->getCollection()
//                    ->addFieldToFilter('active', array('eq' => 1))
//                    ->addFieldToFilter('department_id', array('eq' => $_GET['id']));
//            foreach ($collection as $department) {
//                $requireLogin = $department->getRequiredLogin();
//            }
//        }

        // department required login
//        if (isset($requireLogin) && $requireLogin == 1 && !Mage::getSingleton('customer/session')->isLoggedIn()) {
//            $html = '<div style="text-align:center;">This type of support is provided only for registered users. Please login to access this help desk section.<br />';
//            $html .= '<button onclick="window.location=\'' . Mage::getUrl('customer/account/login')
//                    . '\';" class="button" type="button"><span><span>Login</span></span></button></div>';
//        } else {
//            $html = '<div class="fieldset" >
//	    	<h2 class="legend">Submit Ticket</h2>
//	        <ul class="form-list">';
//
//            // Th contact form
//            if (isset($_GET['url']) && !Mage::getSingleton('customer/session')->isLoggedIn()) {
//                $html .= '<li class="fields">
//				                <div class="field">
//				                    <label for="name" class="required"><em>*</em>Name</label>
//				                    <div class="input-box">
//				                        <input name="name" id="name" value="" class="input-text required-entry" type="text" />
//				                    </div>
//				                </div>
//				                <div class="field">
//				                    <label for="sender" class="required"><em>*</em>Email</label>
//				                    <div class="input-box">
//				                        <input name="sender" id="sender" value="" class="input-text required-entry validate-email" type="text" />
//				                    </div>
//				                </div>
//				            </li>';
//            }
//
//            if (Mage::getStoreConfig('helpdesk/client_config/priority')) {
//                $html .=
//                        '<li class="fields">
//		            	<label for="priority">Priority</label>
//		               	<div class="input-box">
//		                  	<select id="priority" name="priority">
//                                        ';
//               foreach(Mage::getSingleton('helpdesk/config_source_priority')->getOptionArray() as $option => $label){
//                   $html .= '<option value="'.$option.'">'.$label.'</option>';
//               }
//               $html .= ' </select>
//		            	</div>
//		        	</li>';
//            }
//
//            $collection = Mage::getModel('sales/order')->getCollection()
//                    ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
//                    ->setOrder('increment_id', 'DESC');
//            if (Mage::getStoreConfig('helpdesk/client_config/order') && $collection->getSize()) {
//                $orderhtml = '';
//                foreach ($collection as $order) {
//                    $orderhtml .=
//                            '<option value="'
//                            . $order->getId()
//                            . '">#'
//                            . $order->getIncrementId()
//                            . " (\${$order->getGrandTotal()}) "
//                            . ' on ' . date('F jS, Y', Mage::getModel('core/date')->timestamp($order->getCreatedAt()))
//                            . '</option>';
//                }
//                $html .=
//                        '<li class="fields">
//		        		<div class="field">
//			            	<label for="order">Assign To Order #</label>
//			               	<div class="input-box">
//			                  	<select id="order" name="order">
//			                  		<option value="" selected="selected">-- Please select --</option>'
//                        . $orderhtml
//                        . '</select>
//			            	</div>
//		            	</div>
//		            	<div class="field"><br />
//							<a id="linkOrder" href="#" target="">View order detail</a>
//						</div>
//		        	</li>';
//            }
//
//            $html .='<li class="wide">
//	                <label for="subject" class="required"><em>*</em>Subject</label>
//	                <div class="input-box">
//	                    <input type="text" class="input-text required-entry" name="subject" id="subject" />
//                        </div>
//	            </li>
//	            <li class="wide">
//	                <label for="content" class="required"><em>*</em>Message</label>
//	                <div class="input-box">
//	                    <textarea class=" required-entry textarea" cols="15" rows="2" style="width: 100%; height: 150px;" title="Content" name="content" id="content"></textarea>
//	                </div>
//	            </li>';
//            $html .= Mage::helper('helpdesk')->getContentEditor('content');
//            if (Mage::getStoreConfig('helpdesk/coient_config/upload')) {
//                $html .='<li class="fields">
//		                <label for="file_attachment">'.Mage::helper('helpdesk')->__('Attach File').'</label>
//                         </li>';
//                $html .= Mage::helper('helpdesk')->getMultiFileUploader();
//            }
//            $html .=
//                    '</ul>
//	        	<div class="buttons-set">
//		        	<button type="submit" class="button"><span><span>Submit New Ticket</span></span></button>
//		        </div>
//                    </div>';
//        }
//        echo $html;
    }
    

	public function updateloginAction()
	{
		// get user's email logined
        $email = $this->getRequest()->getPost('email');
        // get user's password logined
		$password = $this->getRequest()->getPost('password');
		$dept_id = $this->getRequest()->getPost('dept_id');
		//Mage::getSingleton('core/session')->setDeptid($dept_id);
		//Mage::log("Email: " . $email . ' Password: ' . $password);
        if ($this->getRequest()->isPost()) {
            if (!empty($email) && !empty($password)) {
				try{
					Mage::getSingleton('customer/session')->login($email, $password);
				}catch(Mage_Core_Exception $e){					
				}catch(Exception $e){					
				}
			}
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
			$is_login = "1";
		}
		else{
			$is_login = "0";
		}
		
		$result = array();
		array_push( $result, array(
					'is_login' => $is_login
			)
		);
		echo json_encode($result);
	}
}
<?php

class MW_Helpdesk_Model_Email {

    private $_mailGateway = array();
    private $_mailNoDelGateway = array();
    private $_inbox = array();

    private function _fromDecoder($from) {
	/*
        $from = htmlentities($from);
        $from = explode("&lt;", $from);
        $patterns = array("<", ">", "&gt;", "&lt;");
        return trim(str_replace($patterns, '', $from[1]));
	*/
    }

    private function _toDecoder($to) {
	/*
        $to = htmlentities($to);
        $to = explode("&lt;", $to);
        $patterns = array("<", ">", "&gt;", "&lt;");
        if (sizeof($to) > 1) {
            return trim(str_replace($patterns, '', $to[1]));
        }
        if (is_array($to)) {
            return $to[0];
        }
        return $to;
	*/
    }
	
	private function extractEmail($string) {
        preg_match_all('/[^\s<>]+@[^\s<>]+/', $string, $arr, PREG_PATTERN_ORDER);
        if (isset($arr[0][0])) {
            return $arr[0][0];
        } else {
            return $string;
        }
    }

	private function extractEmailGateway($string) {
		$pos = strpos($string,'Delivered-To:');
		$kt = false;
		if($pos !== false)
		{	
			if($pos == 0)$kt = true;
		}
		if($kt) {
				preg_match_all('/Delivered-To:(\s*)[^\s<>]+@[^\s<>]+/', $string, $arr, PREG_PATTERN_ORDER);
		}
		else 
		{
			preg_match_all('/for(\s*)<[^\s<>]+@[^\s<>]+>/', $string, $arr, PREG_PATTERN_ORDER);
		}
        if (isset($arr[0][0])) {
            return $arr[0][0];
        } else {
            return $string;
        }
    }
	
	private function getEmailGatewayFromRawHeader($string) {
		$str_pare_gateway_email = $this->extractEmailGateway($string);
		$to = '';
		$pos = strpos($string,'Delivered-To:');
		$kt = false;
		
		if($pos !== false)
		{	
			if($pos == 0)$kt = true;
		}

		if($kt){
				$gateway_email = explode(":", $str_pare_gateway_email);
				if(sizeof($str_pare_gateway_email)>0) $to = trim($gateway_email[1]);
		}
		else{ 
			$gateway_email = explode("<", $str_pare_gateway_email);
			$to = trim(trim($gateway_email[1],">"));
		}
		return $to;
	}
	
	private function getReplyToFromRawHeader($string) {
		$reply_to = '';
		$regex_pattern = "/(\s)(Reply-To:)|(\s)(Reply-to:)/";
		$chars = preg_split($regex_pattern, $string);

		if(sizeof($chars)>1) $reply_item = trim($chars[1]);

		if(isset($reply_item)) $replyfrom_arr = explode("\r", $reply_item);
		if(isset($replyfrom_arr))
			if(sizeof($replyfrom_arr) > 0) 
			{
				$reply_to = $replyfrom_arr[0];
			}
		return $reply_to;
	}
	
    private function _nameDecoder($from) {
        $from = htmlentities($from);
        $from = explode("&lt;", $from);
        $patterns = array("<", ">", "&gt;", "&lt;");
        return trim(str_replace($patterns, '', $from[0]));
    }

    private function _subjectDecoder($subject) {
        $subject = imap_mime_header_decode($subject);
        return $subject[0]->text;
    }

	/*
    private function _contentDecoder($part) {
        if($part instanceof Zend_Mail_Part){
            $content = $part->getContent();
			//Mage::log('Check Contentype: ' . Zend_debug::dump($part)) ;
            if ($part->headerExists('content-transfer-encoding')) {
                switch ($part->contentTransferEncoding) {
                    case 'quoted-printable':
                        $content = quoted_printable_decode($content);
                        break;
                    case 'base64':
                        $content = base64_decode($content);
                        break;
                }
            }
        }else{			
            $content = $part;
        }
        return $content;
    }
	*/

	private function _contentDecoder($part) {
        if ($part instanceof Zend_Mail_Part) {
            $content = $part->getContent();
            if ($part->headerExists('content-transfer-encoding')) {
                switch ($part->contentTransferEncoding) {
                    case 'quoted-printable':
                        $content = quoted_printable_decode($content);
                        break;
                    case 'base64':
                        $content = base64_decode($content);
                        break;
                }
            } else {
                $content = $part->getContent();
            }
			
            preg_match_all('/charset=([^\s;]+)/', $part->contentType, $array, PREG_PATTERN_ORDER);
			if(isset($array[1][0])) $from_charset = trim($array[1][0], '"');

            if (isset($from_charset) && strcasecmp($from_charset, 'UTF-8') != 0) {
                $content = iconv($from_charset, 'UTF-8', $content);
            }
        } else {
            $content = $part;
        }
        
        return $content;
    }

	
    /**
     * reply mail get info mail from Email, deliver mail for customer or member
     * 
     * @param string $to
     * @param string $template
     * @param array $data
     * @return void
     */
    
	public function getSenderNameGateway($from){
		$gateways = Mage::getResourceModel('helpdesk/gateway_collection')
                ->addFieldToFilter('active', array('eq' => 1))
				->addFieldToFilter('email', array('eq' => $from));
		if(sizeof($gateways)>0){
			foreach ($gateways as $gateway) {
				return $gateway->getSenderName();
			}
		}
		return '';
	}
    
    public function sentMail($from, $to, $templateId, $dataInbox) {
        $storeId = Mage::app()->getStore()->getId();

        //$sender = array('name' => $from,
        //    'email' => $from);

    	$sender_name = $this->getSenderNameGateway($from);
		if($sender_name != ''){
			$sender = array('name' => $sender_name,
				'email' => $from);
		} else {
			$sender = array('name' => $from,
				'email' => $from);
		}
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        try {
            $emailTemplate = Mage::getModel('core/email_template');

            // cac du lieu co the thiet lap trong file template gom
            $data = array();
            $data['subject'] = $dataInbox['subject'];
            $data['message'] = $dataInbox['message'];
			if(isset($data['order_id']))
			{
				$data['order_id'] = $dataInbox['order_id'];
			}
			
			if(isset($dataInbox['email_ref_id']))
			{
				$data['email_ref_id'] = nl2br($dataInbox['email_ref_id']);
			}
			
			if(isset($dataInbox['ticket_id']))
			{
				$data['ticket_id'] = nl2br($dataInbox['ticket_id']);
			}
			
            $data['store_name'] = Mage::app()->getStore()->getName();
            $data['department_name'] = $dataInbox['department_name'];			
			
            if (isset($dataInbox['operator_name'])) {
                $data['operator_name'] = nl2br($dataInbox['operator_name']);
            }
            if (isset($dataInbox['customer_name'])) {
                $data['customer_name'] = nl2br($dataInbox['customer_name']);
            }
            if (isset($dataInbox['operator_direct_link'])) {
                $data['operator_direct_link'] = $dataInbox['operator_direct_link'];
            }
            if (isset($dataInbox['customer_direct_link'])) {
                $data['customer_direct_link'] = $dataInbox['customer_direct_link'];
            }
            if (isset($dataInbox['expired_time'])) {
                $data['expired_time'] = $dataInbox['expired_time'];
            }
            if (isset($dataInbox['late'])) {
                $data['late'] = $dataInbox['late'];
            }
            if (isset($dataInbox['staff_name'])) {
                $data['staff_name'] = $dataInbox['staff_name'];
            }

            if (isset($dataInbox['note'])) {
                $data['note'] = $dataInbox['note'];
            }

            if (isset($dataInbox['file_attachment']) && $dataInbox['file_attachment'] != '') {
                $file_attachments = explode(";", $dataInbox['file_attachment']);
                foreach ($file_attachments as $file_attachment) {
                    $mail = $emailTemplate->getMail();
                    $fileContents =
                            file_get_contents(Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment);
                    $file = $mail->createAttachment($fileContents);
                    $file->filename = $file_attachment;
                    //$file->type = 'application/zip';
                }
            }
			
			/* get name correct in email (for to field) */
			$to_name = null;
			$members = Mage::getResourceModel('helpdesk/member_collection')
                ->addFieldToFilter('active', array('eq' => 1))
				->addFieldToFilter('email', array('eq' => $to));
			if(sizeof($members)>0){
				foreach ($members as $m) {
					$to_name = $m->getName();
					break;
				}
			}
			else {
				$to_name = $data['customer_name'];
			}
			
            $emailTemplate->sendTransactional(
                    $templateId, $sender, $to, $to_name, // name sender, not describle
                    $data, $storeId
            );
            $translate->setTranslateInline(true);
			
        } catch (Exception $e) {
			//Mage::logException($e);
            echo "email not send";
            die();
        }
		
    }

    public function processGateway() {
        $gateways = Mage::getResourceModel('helpdesk/gateway_collection')
                ->addFieldToFilter('active', array('eq' => 1));
        foreach ($gateways as $gateway) {
            if ($gateway->getDeleteEmail() == 2) { // no delete mail
                $type = Mage::getModel('helpdesk/gateway')->connect($gateway);
                if ($type instanceof Zend_Mail_Storage_Imap) {
                    $this->_mailNoDelGateway[] = Mage::getModel('helpdesk/gateway')->connect($gateway);
                } else {
                    $this->_mailGateway[] = Mage::getModel('helpdesk/gateway')->connect($gateway);
                }
            } else {
                $this->_mailGateway[] = Mage::getModel('helpdesk/gateway')->connect($gateway);
            }
        }
    }

    public function processGetMail() {
    	try {
        $this->processGateway();
		$name = '';
        $flag = false; // have got email
        foreach ($this->_mailGateway as $mail) {
            if ($mail->countMessages() > 0) {
				
                $keepAlive = 0;
                $flag = true;
                for ($n = $mail->countMessages(); $n > 0; $n--) {
                    try {
                        $this->extractEmailInfo($mail, $n);
                        // keep alive
                        if ($keepAlive % 5 == 0)
                           $mail->noop();
                           $keepAlive++;
						   
						// delete mail
						$mail->removeMessage($n);
                    } catch (Exception $e) {
                       // Mage::log('test email here ' . $message);
                    }                    
                }

                // delete mail
				/*
                for ($i = count($mail); $i; --$i) {
					Mage::log('Delete ' . $i);
                    $mail->removeMessage($i);
                }
				*/
            }
        }

        // no delete mail
        foreach ($this->_mailNoDelGateway as $mail) {
            $numberEmail = 50;
            if ($mail->countMessages() > 0) {
                $keepAlive = 0;
                $flag = true;
                if ($mail->countMessages() < $numberEmail) {
                    $numberEmail = 0;
                } else {
                    $numberEmail = $mail->countMessages() - $numberEmail;
                }
                for ($n = $mail->countMessages(); $n > $numberEmail; $n--) {
                    try {
						$message = $mail->getMessage($n);
                        if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) { // had read		   				
                            continue;
                        }
                        $this->extractEmailInfo($mail, $n);
						
                        $mail->setFlags($n, array(Zend_Mail_Storage::FLAG_SEEN)); // set had read
                        // keep alive
                        if ($keepAlive % 5 == 0)
                            $mail->noop();
                        $keepAlive++;
                    } catch (Exception $e) {
                        Mage::log('Test here: ' . $message->to);
                    }
                }
            }
        }

        // call save ticket if exist email
        if ($flag) {
            $this->saveTicket($flag);
        }
      } catch (Exception $e) {
          Mage::log('Process getMail: ' . $e);
      }
      return $flag;
    }

    public function extractEmailInfo($mail, $n) {
        $message = $mail->getMessage($n);                        
        $date = date("Y-m-d h:i:s", Mage::getSingleton('core/date')->timestamp($message->date));
        $from = $this->extractEmail($message->from);
        $helper = Mage::helper('helpdesk/data');

        if(Mage::getSingleton('helpdesk/spam')->checkSpam($from)){
            return;
        }
        
		$name = $this->_nameDecoder($message->from);

        if (Mage::getStoreConfig('helpdesk/email_config/reply_to')) {
			if ($this->getReplyToFromRawHeader($mail->getRawHeader($n)) != '') {
                $from = $this->extractEmail($this->getReplyToFromRawHeader($mail->getRawHeader($n)));
				$name = $this->_nameDecoder($this->getReplyToFromRawHeader($mail->getRawHeader($n)));
				//Chuan hoa ten
				$pos_mail = strpos($name,'@');
				if($pos_mail !== false) $name = '';
            }
        }
        
        try {
            $subject = $this->_subjectDecoder($message->subject);
        } catch (Exception $e) {
            Mage::log($subject);
        }
        				
		try{
			$envelopeTo = $message->envelopeTo; 
			if(isset($envelopeTo)){
				$to = $this->extractEmail($envelopeTo);
			}
		} catch (Exception $e) {
			$to = '';
        }

        if($to == '') 
        	$to = $this->getEmailGatewayFromRawHeader($mail->getRawHeader($n));

        $plainContent = null;
        $htmlContent = null;
        $file_attachment = '';
        $file_attachments = '';

        if ($message->isMultipart()) {
			$files = array();
			foreach (new RecursiveIteratorIterator($message) as $part) {
				switch (strtok($part->contentType, ';')) {
					case 'text/plain':
						$plainContent = $this->_contentDecoder($part);
						/* receive .txt file */										
						$file_attachment = $part->getHeader('content-type');
						preg_match_all('/"([^\"]+)"/', $file_attachment, $matchesarray, PREG_SET_ORDER);
						if(isset($matchesarray[0][1])) $file_attachment = $matchesarray[0][1];
						if($file_attachment) 
						{
							preg_match('/.txt$/', $file_attachment, $matches);
							if(sizeof($matches)>0){
								// upload the attachment
								$content = $this->_contentDecoder($part); // get content
								//$path = Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment;
								$path = $helper->getTicketMediaDir() . $file_attachment;
								/*		insert_here: process duplicate file name		*/
								$at = $path;
								if(file_exists($at))
								{
									$duplicate_filename = TRUE;
									$i=0;
									while ($duplicate_filename)
									{
										$filename_data = explode(".", $file_attachment);
										$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
										$file_attachment = $new_filename;
										$at = Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment;
										if(file_exists($at))
										{
											$i++;
										}
										else
										{
											$duplicate_filename = FALSE;
										}
									}
								}
								//$path = Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment;
								$path = $helper->getTicketMediaDir() . $file_attachment;
								$fh = fopen($path, 'w');
								fwrite($fh, $content);
								fclose($fh);
								//multi file attachment
								$files[] = date("Y"). DS. date("m") .DS . $file_attachment;
							}
						}
						break;
					case 'text/html':
						$htmlContent = $this->_contentDecoder($part);
						/* receive .htm or .html file */										
						$file_attachment = $part->getHeader('content-type');
						preg_match_all('/"([^\"]+)"/', $file_attachment, $matchesarray, PREG_SET_ORDER);
						if(isset($matchesarray[0][1])) $file_attachment = $matchesarray[0][1];
						if($file_attachment) 
						{
							preg_match('/.html$|htm$/', $file_attachment, $matches);
							if(sizeof($matches)>0){
								// upload the attachment
								$content = $this->_contentDecoder($part); // get content
								$path = $helper->getTicketMediaDir() . $file_attachment;
								/*		insert_here: process duplicate file name		*/
								$at = $path;
								if(file_exists($at))
								{
									$duplicate_filename = TRUE;
									$i=0;
									while ($duplicate_filename)
									{
										$filename_data = explode(".", $file_attachment);
										$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
										$file_attachment = $new_filename;
										$at = Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment;
										if(file_exists($at))
										{
											$i++;
										}
										else
										{
											$duplicate_filename = FALSE;
										}
									}
								}
								$path = $helper->getTicketMediaDir() . $file_attachment;
								$fh = fopen($path, 'w');
								fwrite($fh, $content);
								fclose($fh);
								//multi file attachment
								$files[] = date("Y"). DS. date("m") .DS . $file_attachment;
							}
						}
						break;
					default: //attachment handle
						$file_attachment = $part->getHeader('content-type');
						preg_match_all('/"([^\"]+)"/', $file_attachment, $matchesarray, PREG_SET_ORDER);
						$file_attachment = $matchesarray[0][1];
						// upload the attachment
						$content = $this->_contentDecoder($part); // get content
						$path = $helper->getTicketMediaDir() . $file_attachment;
						/*		insert_here		*/
						$at = $path;
						if(file_exists($at))
						{
						  	$duplicate_filename = TRUE;
							$i=0;
							while ($duplicate_filename)
							{
								$filename_data = explode(".", $file_attachment);
								$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
								$file_attachment = $new_filename;
								$at = Mage::getBaseDir('media') . DS . 'ticket' . DS . $file_attachment;
								if(file_exists($at))
								{
									$i++;
								}
								else
								{
									$duplicate_filename = FALSE;
								}
							}
						}
						$path = $helper->getTicketMediaDir() . $file_attachment;
						$fh = fopen($path, 'w');
						fwrite($fh, $content);
						fclose($fh);
						//multi file attachment
						$files[] = date("Y"). DS. date("m") .DS . $file_attachment;
						break;
				}
			}
			$file_attachments = implode(';', $files);
		} else {
			$plainContent = $this->_contentDecoder($message);
		}
		if (!empty($htmlContent)) {
			$plainContent = $this->refineEmailContent($htmlContent);
		}

        $this->_inbox[] = array(
            'created_time' => $date,
            'name' => $this->_subjectDecoder($name),
            'sender' => $from,
            'to' => $to,
            'subject' => $subject,
            'content' => $plainContent,
            'file_attachment' => $file_attachments);    
    
    }

    public function saveTicket() {
        foreach ($this->_inbox as $dataInbox) {
			//$content = explode("========= Please enter", $dataInbox['content']);
			//preg_match('/(\=+)(\s*)((\w+|\.|\,|\?|\;|\-)(\s*))+(\=+)/', $dataInbox['content'], $matches);
			preg_match('/(\={3,})(\s*)((\w+|\.|\,|\?|\;|\-)(\s*))+(\={3,})/', $dataInbox['content'], $matches);
        	if(isset($matches[0])){
				$content = explode($matches[0], $dataInbox['content']);
			}
			else{
				$content = explode("========= Please enter", $dataInbox['content']);
			}
			$helper = Mage::helper('helpdesk/data');
            if (sizeof($content) != 1) {
                $dataInbox['content'] = $content[0];
            }
            $dataInbox['content'] = $this->refineEmailContent($dataInbox['content']);
			//*** update code: filter code_id of ticket from subject
			$regex_pattern = "/(\w*)\#(\d+)(\s*)/";
			preg_match($regex_pattern, $dataInbox['subject'], $matches);
			if(sizeof($matches)>0) $sub = trim($matches[0]);
			else $sub = '';
			if($sub =='' || $sub =='#'){
				$sub = Mage::helper('helpdesk')->parseTicketId($dataInbox['content']);
			}
			$ticketId ='';
			$tickets = '';
			if($sub != ''){
				$tickets = Mage::getModel('helpdesk/ticket')->getCollection()
							->addFieldToFilter('code_id', array('like' => "%$sub%"));
				if(sizeof($tickets)>0) {
					foreach ($tickets as $ticket) {
						$ticketId = $ticket->getId();
					}
				}
			}

            $sub_new = explode(" - ", $dataInbox['subject']);	
			
            if ($ticketId != '') {
                //save to history
				$dataInbox['sub'] = $sub;
				Mage::getModel('helpdesk/history')->saveHistoryFromGateway($dataInbox);
			}
            else {
                // neu cho phep tao moi ticket tu mail
				if (Mage::getStoreConfig('helpdesk/email_config/creticket_email')) {
					if(sizeof($sub_new) > 1) 
						$dataInbox['subject'] = $sub_new[sizeof($sub_new) - 1];
					Mage::getModel('helpdesk/ticket')->saveTicketFromGateway($dataInbox);
				}
			}	
        }
    }
    
    public function refineEmailContent($emailContent){
        $tagHtml = $this->getTagHtml();      
        preg_match_all('/<\/?([a-zA-Z0-9]*)\b[^>]*>/', $emailContent, $matchesarray, PREG_OFFSET_CAPTURE);
        $totalTags = count($matchesarray[1]);
        for($index = $totalTags -1; $index >= 0; $index--){
            if(!isset($tagHtml[$matchesarray[1][$index][0]])){
                $emailContent = substr_replace($emailContent, "",$matchesarray[0][$index][1], strlen($matchesarray[0][$index][0]));
            }
        }
        $emailContent = trim($emailContent);
        return $emailContent;
    }

    public function runCron() {
        $flag = $this->processGetMail();
        return $flag;
    }

    // generate notify
    public function generateNotice() {
        $now = time(); // Time via server, no depend of Timezone setup by Magento
        $noticeTime = (int) Mage::getStoreConfig('helpdesk/config/notice');
        $completeTime = (int) Mage::getStoreConfig('helpdesk/config/complete');
        $stepNoticeTime = (int) Mage::getStoreConfig('helpdesk/config/step_notice');
        $noticeTime = $noticeTime * 60 * 60;
        $stepNoticeTime = $stepNoticeTime * 60 * 60;
        $completeTime = $completeTime * 60 * 60;

		/* comment: not sent notify late ticket to staff with state CLOSED */
        //$collection = Mage::getResourceModel('helpdesk/ticket_collection')
        //        ->addFieldToFilter('status', array('neq' => MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED));
		
		/* not sent notify late ticket to staff with state CLOSED and ONHOLD */
		$collection = Mage::getResourceModel('helpdesk/ticket_collection')
				->addFieldToFilter('status',array('neq'=>MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED))
				->addFieldToFilter('status',array('neq'=>MW_HelpDesk_Model_Config_Source_Status::STATUS_ONHOLD));

        foreach ($collection as $ticket) {
            $lastReplyTime = strtotime($ticket->getLastReplyTime());
            $diff = $now - $lastReplyTime;

            $stepReplyTime = strtotime($ticket->getStepReplyTime());
            $diff2 = $now - $stepReplyTime;

            // customer reply => sent mail for member
            // Dam bao ngay nghi la ko dua notify 
            $weekends = Mage::getStoreConfig('helpdesk/support_time/weekend');
            $weekend = explode(',', $weekends);
            //$today = date('l', time());
            $today = date('w', time());
            if(intval($ticket->getTimeToTicket()) > 0){
                $noticeTimeValue = intval($ticket->getTimeToTicket())*60*60;
            }else{
                $noticeTimeValue = $noticeTime;
            }

            //if ($ticket->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN && in_array($today, $weekend)) {
			if (($ticket->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_OPEN || $ticket->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_NEW) && in_array($today, $weekend)) {
                if (($noticeTimeValue > 0 && $diff > $noticeTimeValue && $lastReplyTime > $stepReplyTime)
                        || ($stepNoticeTime > 0 && $diff2 > $stepNoticeTime && $lastReplyTime < $stepReplyTime)) {
                    // if department enable sent mail notice for member
                    $department = Mage::getModel('helpdesk/department')->load($ticket->getDepartmentId());
                    if ($department->getAutoNotification() == 1) {
                        if ($ticket->getMemberId() != 0) {
                            $members = Mage::getResourceModel('helpdesk/member_collection')
                                    ->addFieldToFilter('member_id', array('eq' => $ticket->getMemberId()));
                            foreach ($members as $member) {
                                // Sent ticket if not solved
                                $mailData = array();
                                $to = $member->getEmail();
                                $mailData['operator_name'] = $member->getName();
                                $mailData['customer_name'] = $ticket->getName();
                                $mailData['subject'] = $ticket->getCodeId() . ' - ' . $ticket->getSubject();
                                $mailData['message'] = $ticket->getContent();
                                $diff = $diff / 60 / 60;
                                $mailData['late'] = round($diff);

                                //get last email(history) sent if exist
                                $historyCollection = Mage::getModel('helpdesk/history')->getCollection()
                                        ->addFilter('ticket_id', $ticket->getId())
                                        ->setOrder('history_id', 'DESC');
                                if ($historyCollection->getSize() > 0) {
                                    foreach ($historyCollection as $history) {
                                        $mailData['message'] = $history->getContent();
                                        break;
                                    }
                                }

                                //$mailData['operator_direct_link'] = Mage::getBaseUrl() . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();

								$helper = Mage::helper('helpdesk/data');
								$base_url = $helper->_getBaseUrl($ticket->getStoreView());
								$mailData['operator_direct_link'] = $base_url . 'helpdesk/viewticket/moderator/code/' . $ticket->getCodeMember();
								
                                $gateway = Mage::getModel('helpdesk/gateway')->load($department->getDefaultGateway());
                                $from = $gateway->getEmail(); // email tuong ung
                                $mailData['department_name'] = $department->getName();
                                $templateId = $department->getLateReplyTicketOperator();
                                if ($templateId == '') {
                                    $storeId = Mage::app()->getStore()->getId();
                                    $template = 'helpdesk/helpdesk_email_temp/late_reply_ticket_operator';
                                    $templateId = Mage::getStoreConfig($template, $storeId);
                                }

                                $this->sentMail($from, $to, $templateId, $mailData);

                                $ticket = Mage::getModel('helpdesk/ticket')->load($ticket->getId());
                                $ticket->setTimeToProcess(0);
                                $ticket->setStepReplyTime(now())->save();
                            }
                        }
                    }
                }
            }

            if ($ticket->getStatus() == MW_HelpDesk_Model_Config_Source_Status::STATUS_PROCESSING) {
                if ($diff > $completeTime) {
                    //if( $diff > ($completeTime * 60) ){
                    // change status STATUS_COMPLETE
                    $ticket = Mage::getModel('helpdesk/ticket')->load($ticket->getId());
                    $ticket->setStatus(MW_HelpDesk_Model_Config_Source_Status::STATUS_CLOSED)->save();
                }
            }
        }
    }
    
    public function getTagHtml(){
        if(!Mage::registry('mw_hd_tag_html')){
            $tagHtmlConfig = Mage::getStoreConfig('helpdesk/email_config/tag_html');
            $tags = explode(';', $tagHtmlConfig);
            foreach($tags as $tag){
                $tagHtml[$tag] = 1;
            }
            Mage::register('mw_hd_tag_html', $tagHtml);
        }
        return Mage::registry('mw_hd_tag_html');
    }
}
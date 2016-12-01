<?php

class MW_HelpDesk_Helper_Data extends Mage_Core_Helper_Abstract {

	const MYCONFIG = "helpdesk/config/enabled";
	const MYNAME = "MW_HelpDesk";

	public function _convertStoreIdToStoreCode($store_id){
		$stores = array_keys(Mage::app()->getStores());
	 	foreach($stores as $id){
	        $store = Mage::app()->getStore($id);
	        if($store->getStoreId() == $store_id) {
	          	return $store->getCode();
	        }
        }
        return false;
	}
	

	public function _getDefaultBaseUrl()
	{
		$websites = Mage::getModel('core/website')->getCollection();
		foreach($websites as $website)
		{
		    $default_store = $website->getDefaultStore();
		    $url_obj = new Mage_Core_Model_Url();
		    $default_store_path = $url_obj->getBaseUrl(array('_store'=> $default_store->getCode()));
			return $default_store_path;
		}

	}
	
	public function convOp2Sql($op){
		switch ($op) {
			case '==': return "=";
            case '!=': return "!=";
            case '>=': return ">=";
            case '<=': return "<=";
            case '>' : return ">";
            case '<' : return "<";
            case '{}' : return "like";
            case '!{}' : return "not like";
            case '()': return "in";
            case '!()':return "not in";
			case 'REGEXP':return "REGEXP";
            default: return null;
		}
	}
	
	public function parseTicketId($text){
		$regex_pattern1 = "/Ticket ID:(\s*)(\w*)\#(\d+)(\s*)/";
		preg_match($regex_pattern1, $text, $matches1);
		if(sizeof($matches1)>0)$sub1 = trim($matches1[0]);
		else $sub1 = '';
		if($sub1 != ''){
			$pos = strpos($sub1, ":");
			$sub2 = trim(substr($sub1, $pos+1, strlen($sub1)));
			return $sub2;
		}
	}
	//pare email send from ebay
	public function isEbayMail($str_subject){
		preg_match('/^(\w+)(\@)(\w+)(\.ebay.)(\w+)/', $str_subject, $matches);
		if(sizeof($matches)>0)return strlen($matches[0]);
		return 0;
	}
	
	public function getItemIdFromEmailEbay($str){
//		$regex_pattern1 = "/Item Id:\s+(\d+)/";
//		preg_match_all($regex_pattern1, $str, $matches1);
//		$str2 = $matches1[0];
//		$regex_pattern2 = "/\s+(\d+)/";
//		preg_match_all($regex_pattern2, $str2, $matches2);
//		return $matches2[0];

		$s1 = explode("End time:", $str);
		$s2 = $s1[0];
		$s3 = explode("Item Id:", $s2);
		return trim($s3[1]);
	}

	public function getBuyerFromEmailEbay($str){
//		$regex_pattern1 = "/Buyer:\s+(\w+)/";
//		preg_match($regex_pattern1, $str, $matches1);
//		$str2 = $matches1[0];
//		$regex_pattern2 = "/\s+(\w+)/";
//		preg_match($regex_pattern2, $str2, $matches2);
//		return trim($matches2[0]);
		if(strpos($str,"Buyer email address:")){
			if(strpos($str,"Listing Status:") > strpos($str,"Buyer email address:")){
				$s1 = explode("Buyer email address:", $str);
			}
			else {
					$s1 = explode("Listing Status:", $str);
			}
		}
		else
			$s1 = explode("Listing Status:", $str);
		$s2 = $s1[0];
		$s3 = explode("Buyer:", $s2);
		
		$s5 = '';

		if(strpos($s3[1],"<br />")> 0){
			$s4 = explode("<br />", $s3[1]);
			$s5 = $s4[0];
		}
		else $s5 = $s3[1];
		if(strpos($s5,"</p>") > 0){
			$s6 = explode("</p>", $s5);
			$s7 = $s6[0];
		}
		else $s7 = $s5;
		
		return trim($s7);
	}
	
	public function getEmailRefIdFromEmailEbay($str){
		$regex_pattern1 = "/(\[#)(\w+)\-(\w+)(\#])_(\[#)(\w+)(\#])/";
		preg_match($regex_pattern1, $str, $matches1);
		if($matches1[0] != '') return $matches1[0];
		else 
		{
			$regex_pattern1 = "/(\[#)(\w+)(\#])/";
			preg_match($regex_pattern1, $str, $matches1);
			return $matches1[0];
		}
	}


    public function addTicketButton() {
    	$allow = Mage::getSingleton('admin/session');
		//1: allow < --- > '': not allow
		$is_allow = $allow->isAllowed('helpdesk');
		if($is_allow == 1){
	        $url = Mage::helper('adminhtml')->getUrl('adminhtml/hdadmin_ticket/new/action/open', array('customer_id' => Mage::registry('current_customer')->getId()));
	        return array(
	            'label' => $this->__('Add Ticket'),
	            'onclick' => "window.open('$url')",
	        );
		}
		else{
			return array(
	            'label' => $this->__('Add Ticket'),
	            'disabled' => true,
        	);
		}
    }

    public function addTicketButtonOrder() {
    	$allow = Mage::getSingleton('admin/session');
		//1: allow < --- > '': not allow
		$is_allow = $allow->isAllowed('helpdesk');
		if($is_allow == 1){
	        $url = Mage::helper('adminhtml')->getUrl('adminhtml/hdadmin_ticket/new/action/open', array('oid' => Mage::registry('sales_order')->getId()));
	        return array(
	            'label' => $this->__('Add Ticket'),
	            'onclick' => "window.open('$url')",
	        );
    	}
		else{
			return array(
	            'label' => $this->__('Add Ticket'),
	            'disabled' => true,
        	);
		}
    }

    public function getContentEditor($textAreaId) {
        return
                '<script type="text/javascript">  
                tinyMCE.init({
                    mode : "textareas",
                    theme : "advanced",
                    elements : "' . $textAreaId . '",
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_buttons1 : "bold ,italic,underline,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,forecolor,backcolor,cleanup, fontsizeselect",
                    theme_advanced_buttons2 : "",
                    theme_advanced_buttons3 : "",
                    theme_advanced_font_sizes : "13px,14px,16px,24px",
                });
            </script>';
    }

    public function prepareMemberDataForAutocomplete($textFieldId, $populateDivId) {
        return
                '<script type="text/javascript">
             var memberList = ' . Mage::getSingleton('helpdesk/member')->getMemberDataForAutocomplete() . ';
             new Autocompleter.Local("' . $textFieldId . '", "' . $populateDivId . '", memberList, {fullSearch:true, partialSearch:true, partialChars:0,
                 updateElement:autocomplete});
             function autocomplete(selectedElement){
                value = Element.collectTextNodesIgnoreClass(selectedElement, \'informal\');
                matchData = value.match(/[^\s]+@[^\s]+/);    
                $(\'' . $textFieldId . '\').value = matchData[0];
             };
         </script>';
    }

    public function prepareOrderDataForAutocomplete($textFieldId, $populateDivId) {
        return
         '<script type="text/javascript">
             var memberList = ' . Mage::getSingleton('helpdesk/ticket')->getOrderDataForAutocomplete() . ';
             new Autocompleter.Local("' . $textFieldId . '", "' . $populateDivId . '", memberList, {fullSearch:true, partialSearch:true, partialChars:1,
                 updateElement:autocomplete});
             function autocomplete(selectedElement){
                value = Element.collectTextNodesIgnoreClass(selectedElement, \'informal\');
                matchData = value.match(/\d+/);    
                $(\'' . $textFieldId . '\').value = matchData[0];
             };   
         </script>';
    }
    
    
    public function getBackendUploaderContainer($fileAttachmentParam) {
        $templateElementUpload = '<li style="list-style:none"><input type="file" name="' . $fileAttachmentParam . '[]"/><button style="margin-left:5px" id="mw_helpdesk_delete_upload_button_{{id}}" class="button" type="button"><span><span>' . Mage::helper('helpdesk')->__('Remove') . '</span></span></button></li>';
        $html = '<tr>
            <td class="label"></td>
        <td class="value"><button id="mw_helpdesk_add_uploader_button" class="button" type="button">
            <span><span>' . Mage::helper('helpdesk')->__('Attach a file') . '</span></span>
        </button></td>
    </div>
</tr>
<tr>
    <td class="label"></td>
    <td class="label"><div id="mw_helpdesk_uploadContainner"/></td>';
        $html .= $this->getMultipleFileUploaderJs($templateElementUpload);
        return $html;
    }

    public function getFrontendUploaderContainer($fileAttachmentParam) {
//        $templateElementUpload = '<li><div class="wide"><label></label><input type="file" name="' . $fileAttachmentParam . '[]"/><button style="margin-left:5px; float:none" id="mw_helpdesk_delete_upload_button_{{id}}" class="button" type="button"><span><span>' . Mage::helper('helpdesk')->__('Remove') . '</span></span></button></div></li>';
        /* remove is image button */
    	$button_cancel_path = Mage::getDesign()->getSkinUrl('mw_helpdesk/images/button_cancel.png',array('_area'=>'frontend')); 
    	$templateElementUpload = '<li><div class="wide"><label></label><input type="file" name="' . $fileAttachmentParam . '[]"/><input type="image" src="'.$button_cancel_path.'" name="image" width="25px" height="24px" style ="padding-left: 5px;" id="mw_helpdesk_delete_upload_button_{{id}}"></div></li>';
        $html = '<li class="wide">
				    <div class="wide">
				        <label></label>
				        <button id="mw_helpdesk_add_uploader_button" class="button" type="button">
				            <span><span>' . Mage::helper('helpdesk')->__('Attach a file') . '</span></span>
				        </button>
				        <input type="image" src="'.$button_cancel_path.'" name="image" width="25px" height="24px" style ="display: none;">
				    </div>
				</li>
				<div id="mw_helpdesk_uploadContainner" class="form-buttons"/>';

    	/* attach file is link */
//        $html = '<li class="wide">
//				    <div class="wide">
//				        <label></label>
//				        <a id="mw_helpdesk_add_uploader_button" href="javascript:void(0)">
//				            <span><span>' . Mage::helper('helpdesk')->__('Attach a file') . '</span></span>
//				        </a>
//				    </div>
//				</li>
//				<div id="mw_helpdesk_uploadContainner" class="form-buttons"/>';
        
        $html .= $this->getMultipleFileUploaderJs($templateElementUpload);
        return $html;
    }

    public function getMultipleFileUploaderJs($templateElementUpload) {
        return '
<script type="text/javascript">
    var uploaderTemplate = \'' . $templateElementUpload . '\';

    var mw_helpdesk_uploadContainner = {
        div: $(\'mw_helpdesk_uploadContainner\'),
        templateSyntax: /(^|.|\r|\n)({{(\w+)}})/,
        templateText: uploaderTemplate,
        itemCount: 0,
        remove: function(event){
            var element = $(Event.findElement(event,\'li\'));
            element.remove();
        },
        add: function(data){
            this.itemCount++;
            this.template = new Template(this.templateText, this.templateSyntax);
            if(!data.id){
                data = {};
                data.id = this.itemCount;
            }
            Element.insert(this.div, {\'bottom\':this.template.evaluate(data)});
            Event.observe(\'mw_helpdesk_delete_upload_button_\'+data.id, \'click\', this.remove.bind(this));
        }
    }
    if($(\'mw_helpdesk_add_uploader_button\')){
        Event.observe(\'mw_helpdesk_add_uploader_button\', \'click\', mw_helpdesk_uploadContainner.add.bind(mw_helpdesk_uploadContainner));
    }  
</script>';
    }
    
	public function getFileNameFromFileAttachment($path){
    	$filename = '';
		$filename = substr(strrchr($path, DS), 1);
		if($filename == '') return $path;
		
		return $filename; 
    }
    
    public function getTicketMediaDir(){
    	return  Mage::getBaseDir('media') . DS . 'ticket' . DS. date("Y"). DS. date("m"). DS;
    }
    
    public function processMultiUpload() {
        if (isset($_FILES["file_attachment"]["name"])) {
            if (is_array($_FILES["file_attachment"]["name"])) {
                foreach ($_FILES["file_attachment"]["name"] as $index => $value) {
                    $uploadFiles[$index]['name'] = $_FILES["file_attachment"]["name"][$index];
                    $uploadFiles[$index]['type'] = $_FILES["file_attachment"]["type"][$index];
                    $uploadFiles[$index]['tmp_name'] = $_FILES["file_attachment"]["tmp_name"][$index];
                    $uploadFiles[$index]['error'] = $_FILES["file_attachment"]["error"][$index];
                    $uploadFiles[$index]['size'] = $_FILES["file_attachment"]["size"][$index];
                }

                foreach ($uploadFiles as $uploadFile) {
                    if (isset($uploadFile["name"]) && $uploadFile["name"] != '') {
                        if (Mage::getStoreConfig('helpdesk/client_config/max_upload') != ''
                                && Mage::getStoreConfig('helpdesk/client_config/max_upload') != 0) {
                            $size = $uploadFile['size'] / 1024 / 1024;
                            $maxUpload = Mage::getStoreConfig('helpdesk/client_config/max_upload');
                            if ($maxUpload < $size) {
                                Mage::getSingleton('customer/session')->addError("Max Upload File Size is {$maxUpload} (Mb)");
                                $this->_redirect('helpdesk/account/submit/');
                                return;
                            }
                        }

                        try {
							$str =  preg_replace('/[^a-z.A-Z0-9]/', "_", $uploadFile["name"]);	
							$str = preg_replace('/\_\_+/', '_', $str);
                            //this way the name is saved in DB

                            /* Starting upload */
                            $uploader = new Varien_File_Uploader($uploadFile);

                            // Any extention would work
                            //$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                            $uploader->setAllowRenameFiles(false);
                            // Set the file upload mode 
                            // false -> get the file directly in the specified folder
                            // true -> get the file in the product like folders 
                            //	(file.jpg will go in something like /media/f/i/file.jpg)
                            $uploader->setFilesDispersion(false);
                            // We set media as the upload dir
                            //$path = Mage::getBaseDir('media') . DS . 'ticket' . DS;
							$path = $this->getTicketMediaDir();
							/*		insert_here: process duplicate file name		*/
                            $at = "$path".$str."";
	                        if(file_exists($at))
							{
							  $duplicate_filename = TRUE;
								$i=0;
								while ($duplicate_filename)
								{
									$filename_data = explode(".", $str);
									$new_filename = $filename_data[0] . "_" . $i . "." . $filename_data[1];
									$str = $new_filename;
									$at = "$path".$str."";
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
							/* *********************************** */
							
                            $resul = $uploader->save($path, $str);
							$file_attachments[] = date("Y"). DS. date("m") .DS . $resul["file"];
                        } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('helpdesk')->__('Unable to upload file'));
                        }
                    }
                }
                return implode(';', $file_attachments);
            }
        }
        return false;
    }
    
    /**
     * Use to get frontend's url in backend page. 
     * It correct url when configuration "web/url/use_store" is true.
     * @param String $routPath
     * @param array $params
     * @return String $url
     */ 
    public function getCorrectUrl($routPath = '*/*/*', $params){
        $url = Mage::getUrl($routPath, $params);
        if((strcmp($routPath, '*/*/*') != 0) && Mage::getStoreConfig('web/url/use_store')){
            $post = strpos($url, $routPath);
            $collection = Mage::getSingleton('core/store')->getCollection();
            if(!$collection->isLoaded()){
                $collection->addFieldToFilter('is_active', array('eq' => 1))
                    ->addFieldToFilter('code', array('nlike' => 'admin'));
            }
            $store = $collection->getFirstItem();
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$store->getCode().'/'.substr($url, $post);
        }
        return $url;
    }
	 
	public function myConfig(){
    	return self::MYCONFIG;
    }
	
	function disableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),0); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}

	public function recursiveReplace($search, $replace, $subject)
    {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }
    
    /*
	Uppercase for first letter of day/month name for the locale
	param $date_str example: jeudi 14 mars 2013
	return string  exp: Jeudi 14 Mars 2013
	*/
    public function upppercase_date_string($date_str){
		$date=explode(" ",$date_str);$date_arr=array();
		foreach ($date as $d){
		$date_arr[]=ucfirst($d);		
		};
		return implode(" ",$date_arr);
		
	}
	/*
	Return date/time with correct format for each store view
	param $time int
	param $format const Mage_Core_Model_Locale::FORMAT_TYPE_FULL/FORMAT_TYPE_LONG/FORMAT_TYPE_MEDIUM/FORMAT_TYPE_SHORT	
	return string 
	*/
	public function locale_time_format($time,$format,$time_format=null)
    {        
		$format=Mage::app()->getLocale()->getDateFormat($format);
		$date=$this->upppercase_date_string(Mage::app()->getLocale()->date($time)->toString($format));
		if(!empty($time_format))
		$date=$date." ". date($time_format, $time)." ";		
		return $date;		
    } 

	//param $date_str. example 'Sunday', 'Monday',...
	public function convertDateByLocate($value){
		$store_date = Mage::app()->getLocale()->getOptionWeekdays();
    	$arr_date= array();
    	foreach ($store_date as $d) {
    		if($d['value'] == $value) return $d['label'];
    	}
	}
	
	/*
	Return url with store_id (if has store code)
	param $store_id int
	return string_url
	*/
	
	public function _getBaseUrl($store_id){
		/* if $store_id == 0 will get default url by default store */
		if($store_id == 0) $store_id = 1;
		
		if(Mage::getStoreConfig('web/secure/use_in_frontend') == 1){//neu chon la https
		    return Mage::getUrl('', array('_store' => $store_id, '_secure'=>true, '_nosid' => true));
		}
		return Mage::getUrl('', array('_store' => $store_id, '_nosid' => true));
	}
}
<?php

class MW_HelpDesk_Model_Rules extends Mage_Core_Model_Abstract
{
	protected $_conditions;
    protected $_actions;
    protected $_form;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpdesk/rules');
    }
    
	//check date expire
    public function checkFromDateToDate($fromdate, $todate){
    	$todayDate = Mage::getModel('core/date')->timestamp(time());
	    if($fromdate!='' && $todate!=''){
			$dateStart = Mage::getSingleton('core/date')->timestamp($fromdate); 
			$dateEnd = Mage::getSingleton('core/date')->timestamp($todate);
				if($todayDate>=$dateStart && $todayDate<=$dateEnd) 
				{
					return 1;
				}
	    }
	    return 0;			
    }
    
    //get cac id ticket thoa man rule (truong hop: ALL - TRUE / ANY - TRUE)
    public function getTicketId_Rule($rule_id, $ticket_id){
    	$collections_rule = Mage::getModel('helpdesk/rules')->load($rule_id);
		$conditionsArr = unserialize($collections_rule->getConditionsSerialized());
		// collection to execute sql
    	$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		$collections_tickets = Mage::getModel('helpdesk/ticket')->getCollection();
		$helper = Mage::helper('helpdesk/data');
		if(isset($conditionsArr['conditions'])) $collections_rules = $conditionsArr['conditions'];
		//Zend_debug::dump($conditionsArr['aggregator']);
		//Zend_debug::dump($conditionsArr['value']);
		//neu ko thiet lap conditions thi mac dinh rule do la thoa man
		if(!isset($conditionsArr['conditions'])) return $ticket_id;
		
		$express = '';
		foreach ($collections_rules as $value) {
			//echo $value['attribute'] . ' ---- ' . $value['operator'] . ', ';
			if($value['operator'] == '{}')
				$express .= '(' . $value['attribute'] . ' ' . $helper->convOp2Sql($value['operator']) . ' ' . "'%" . $value['value'] . "%'); ";
			else if($value['operator'] == '!{}')
				$express .= '(' . $value['attribute'] . ' ' . $helper->convOp2Sql($value['operator']) . ' ' . "'%" . $value['value'] . "%'); ";
			else if($value['operator'] == '()' || $value['operator'] == '!()'){
				//check for this 
				$arr_one_of = explode(",", $value['value']);
				$operator_in = '';
				for($i = 0; $i < sizeof($arr_one_of); $i++){
					if(trim($arr_one_of[$i]) != ''){
						$operator_in .= "'" . trim($arr_one_of[$i]) . "',";
					}
				}
				//bo dau , o cuoi xau: substr($operator_in,0,strlen($operator_in)-1)
				$express .= '(' . $value['attribute'] . ' ' . $helper->convOp2Sql($value['operator']) . ' ' . " (" . substr($operator_in,0,strlen($operator_in)-1) . ")); ";
			}
			else 
				$express .= '(' . $value['attribute'] . ' ' . $helper->convOp2Sql($value['operator']) . ' ' . "'" . $value['value'] . "'); ";
		}
		$arr_express = explode(";", $express);
		$express_for_content = '';
		//echo 'Express: ' . $express . '<br />';
    	for($i = 0; $i < sizeof($arr_express)-1; $i++) { 
			if(strpos($arr_express[$i], "content")!==false){
				//echo $arr_express[$i] . "<br />";
				$query1 = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE (' . $arr_express[$i] . ') AND (ticket_id = ' . $ticket_id . ')';
				$query2 = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/history') . ' WHERE (' . $arr_express[$i] . ') AND (ticket_id = ' . $ticket_id . ')';
				$results1 = $readConnection->fetchAll($query1);
				$results2 = $readConnection->fetchAll($query2);
				//echo '1: ' . sizeof($results1) . ' 2: ' . sizeof($results2) . '<br />';
				if(sizeof($results1)>0)$express_for_content .= '(true)' . ';';
				else if(sizeof($results2)>0)$express_for_content .= '(true)' . ';';
				else $express_for_content .= '(false)' . ';';
				//$arr_express_sub = explode(" ", $arr_express[$i]);
				//echo 'Field: ' . trim($arr_express_sub[1],"(") . ' Operator: ' . $arr_express_sub[2] . ' Value: ' . trim($arr_express_sub[3],")") . "<br />";
			}
			else if(strpos($arr_express[$i], "email")!==false){
				$query1 = 'SELECT gateway_id  FROM ' . $resource->getTableName('helpdesk/gateway') . ' WHERE (' . $arr_express[$i] . ')';
				$results1 = $readConnection->fetchAll($query1);
				$department_id_ticket = '';
				if(sizeof($results1)>0){
					foreach ($results1 as $id) {
						$gateway_id = $id["gateway_id"];					
						$model_ticket = Mage::getModel('helpdesk/ticket')->load($ticket_id);
						$department_id_ticket = $model_ticket->getDepartmentId();
						if($department_id_ticket != ''){
							$model_department = Mage::getModel('helpdesk/department')->getCollection()
												->addFieldToFilter('default_gateway', $gateway_id)
												->addFieldToFilter('department_id ', $department_id_ticket);
							if(sizeof($model_department) > 0)$express_for_content .= '(true)' . ';';
							else $express_for_content .= '(false)' . ';';
						}
						break; 
					}
				}
				else $express_for_content .= '(false)' . ';';
				//echo 'aa: ' . $express_for_content;die;
			}
			else if(strpos($arr_express[$i], "increment_id")!==false){
				$query1 = 'SELECT entity_id FROM ' . $resource->getTableName('sales/order') . ' WHERE (' . $arr_express[$i] . ')';
				$results1 = $readConnection->fetchAll($query1);
				
				$kq = false;
		    	if(sizeof($results1) > 0){
		    		$model_ticket = Mage::getModel('helpdesk/ticket')->load($ticket_id);
		    		foreach ($results1 as $id) {
						if($id["entity_id"] == $model_ticket->getOrder())
						{
							$kq = true;
							break;
						}
					}
		    	}
		    	if($kq) $express_for_content .= '(true)' . ';';
		    	else $express_for_content .= '(false)' . ';';
			}
			else if(strpos($arr_express[$i], "member_id")!==false){
    			$new_query = str_replace("member_id", "email", $arr_express[$i]); 
				$query1 = 'SELECT member_id FROM ' . $resource->getTableName('helpdesk/member') . ' WHERE (' . $new_query . ')';
				$results1 = $readConnection->fetchAll($query1);
				//echo $arr_express[$i] . ' - ' . $new_query;die;
				$kq = false;
		    	if(sizeof($results1) > 0){
		    		$model_ticket = Mage::getModel('helpdesk/ticket')->load($ticket_id);
		    		foreach ($results1 as $id) {
						if($id["member_id"] == $model_ticket->getMemberId())
						{
							$kq = true;
							break;
						}
					}
		    	}
		    	if($kq) $express_for_content .= '(true)' . ';';
		    	else $express_for_content .= '(false)' . ';';
			}
			else $express_for_content .= $arr_express[$i] . ';';
		}
		//echo $express_for_content;
//		die;
		$arr_express = explode(";", $express_for_content);
		//Zend_Debug::dump($arr_express);
		//echo 'size: ' . sizeof($arr_express);
		$exp_where = '';
		$exp_where = $arr_express[0];
		if(sizeof($arr_express)>1)
		{
			// all (and)
			if($conditionsArr['aggregator'] == 'all'){
				for($i = 1; $i < sizeof($arr_express)-1; $i++) {
					$exp_where .=  ' AND ' . $arr_express[$i];
				}
				// all (and) - true
				if($conditionsArr['value'] == 1){
					$query = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE (' . $exp_where . ') AND (ticket_id = ' . $ticket_id . ')';
				}
				else{// all (and) - false
					$query = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE NOT (' . $exp_where . ') AND (ticket_id = ' . $ticket_id . ')';
				}
			}
			// any (or)
			else {
				
				for($i = 1; $i < sizeof($arr_express)-1; $i++) {
					$exp_where .=  ' OR ' . $arr_express[$i];
				}
				//echo 'any or: ' . $exp_where . "<br />";
				//die;
				// any (or) - true
				if($conditionsArr['value'] == 1){
					$query = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE (' . $exp_where . ') AND (ticket_id = ' . $ticket_id . ')';
				}
				else{// any (or) - false
					//code here
					$arr_exp_where = explode("OR", $exp_where);
					foreach ($arr_exp_where as $value) {
						$query_any_false = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE ' . trim($value) . ' AND (ticket_id = ' . $ticket_id . ')';
						//echo 'SQL: ' . $query . '<br />';
						//mang result chua cac ticket thoa man rule_id nao do
						$results = $readConnection->fetchAll($query_any_false);
						if(sizeof($results) == 0){//neu phat hien co 1 dieu kien sai thi moi ga'n cau lenh sql
							$query = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE (' . $exp_where . ') AND (ticket_id = ' . $ticket_id . ')';
							break;
						}
						//echo 'sizeof: ' . sizeof($results) . '<br />';						
					}
//					echo $exp_where . ' - sizeof: ' . sizeof($results);
//					die;
				}
			}
		}
		//$query = 'SELECT ticket_id FROM ' . $resource->getTableName('helpdesk/ticket') . ' WHERE (' . $exp_where . ') AND (ticket_id = ' . $ticket_id . ')';
		//echo 'SQL: ' . $query;die;
		//mang result chua cac ticket thoa man rule_id nao do
		$ret_ticket_id = '';
		if($query != ''){
			$results = $readConnection->fetchAll($query);
			foreach ($results as $id) {
				$ret_ticket_id = $id["ticket_id"];
			}
			//Zend_debug::dump($ret_ticket_id);die;
		}
		return $ret_ticket_id;
    }
    
	//do action with ticket id thoa man rule
    public function doActionWithRule($rule_id, $ticket_id, $fromEmail){
    	$model_rule = Mage::getModel('helpdesk/rules')->load($rule_id);
    	$model_ticket = Mage::getModel('helpdesk/ticket');
    	$ticket_id_appropriate = Mage::getModel('helpdesk/rules')->getTicketId_Rule($rule_id, $ticket_id);//results
    	
    	//get param from of ticket from rules
		/*
    	$status = $model_rule->getAcStatus();
    	$priority = $model_rule->getAcPriority();
		$department_id = $model_rule->getAcDepartmentId();
		$member_id = $model_rule->getAcMemberId();
		$tags_name = $model_rule->getAcTagsName();
		$template_id = $model_rule->getAcTemplateId();
		if($status > 0) $model_ticket->setStatus($status);
		if($priority > 0) $model_ticket->setPriority($priority);
		if($department_id > 0) $model_ticket->setDepartmentId($department_id);
		if($member_id > 0) $model_ticket->setMemberId($member_id);			
		//echo $tags_name;die;
		//if($tags_name !='') $model_ticket->setDepartmentId($tags_name);
		if($template_id > 0) $model_ticket->setTemplateId($template_id);
		*/
		
		if($ticket_id_appropriate == '') return 0;
		//foreach ($ticket_ids as $id) {
		if($ticket_id_appropriate != ''){
			//load apply status from rule table
			$model_rule->load($rule_id);
		    //$arr_app_status = explode(",",$model_rule->getAppStatus());
			//$load_ticket_by_id = Mage::getModel('helpdesk/ticket')->load($ticket_id_appropriate);
		    //$status_ticket = $load_ticket_by_id->getStatus();
		    //chi ap dung voi cac ticket co status phu hop voi conditon cua rule
		    //if(in_array($status_ticket, $arr_app_status)){
				$status = $model_rule->getAcStatus();
				$priority = $model_rule->getAcPriority();
				$department_id = $model_rule->getAcDepartmentId();
				$member_id = $model_rule->getAcMemberId();
				$tags_name = $model_rule->getAcTagsName();
				$template_id = $model_rule->getAcTemplateId();
				if($status > 0) $model_ticket->setStatus($status)->setId($ticket_id_appropriate);
				if($priority > 0) $model_ticket->setPriority($priority)->setId($ticket_id_appropriate);
				if($department_id > 0) $model_ticket->setDepartmentId($department_id)->setId($ticket_id_appropriate);
				if($member_id > 0) $model_ticket->setMemberId($member_id)->setId($ticket_id_appropriate);
				//if($template_id > 0) $model_ticket->setTemplateId($template_id)->setId($ticket_id_appropriate);
				
				//$model_ticket->setId($ticket_id_appropriate);
				$model_ticket->save();
				
				//neu ticket gui den thoa man rule thi se gui thu cho staff thong qua member_id
				if($member_id > 0) 
				{
					Mage::getModel('helpdesk/ticket')->reassignTicket($ticket_id_appropriate);
				} 
				
				if($template_id > 0){
					$model_template = Mage::getModel('helpdesk/template')->load($template_id);
					$history = array();
					$history['ticket_id'] = $ticket_id_appropriate;
					$history['content'] = $model_template->getMessage();
					
					$file_attachment = Mage::helper('helpdesk')->processMultiUpload();
					if ($file_attachment) {
						$history['file_attachment'] = $file_attachment;
					}
					 
					Mage::getModel('helpdesk/ticket')->replyTicketFromAdmin($history);
				}
				
				// add tags
			    if (!empty($tags_name)) {
					// before need delete all tags exists
					$collection = Mage::getModel('helpdesk/tag')->getCollection()
							->addFieldToFilter('ticket_id', array('eq' => $ticket_id_appropriate));
					
					$tags = $temp = array();
					$temps = explode(',', $tags_name);
					foreach ($temps as $temp) {
						$temp = trim($temp);
						foreach ($collection as $tag) {
							if($tag->getName() == $temp)
								$tag->delete();
						}
						if (!empty($temp)) {
							$tag = Mage::getModel('helpdesk/tag');
							$tag->setTicketId($ticket_id_appropriate)
									->setName($temp)
									->save();
						}
					  }
				}
				return 1;       
		    //}    
		}
		return 0;
    }
	
	 /* ----------------------- add functions for check rule ------------------------ */
 	//get rule_id theo tuy chon When ticket created / When ticket updated
    public function getRuleId4Submit($is_created_updated, $fromEmail){
    	$collections = Mage::getModel('helpdesk/rules');
    	//load rule When ticket created / When ticket updated and sort priority ascending
    	if($is_created_updated == 'created'){
    		$collections_rule = $collections->getCollection()
    				->addFieldToFilter('is_created',1)
    				->addFieldToFilter('is_active',1)
    				->setOrder('sort_order', 'DESC');
    	}
    	else {
    		$collections_rule = $collections->getCollection()
    				->addFieldToFilter('is_updated',1)
    				->addFieldToFilter('is_active',1)
    				->setOrder('sort_order', 'DESC');
    	}

    	$ret_arr_rule_ids = array();
		
		foreach ($collections_rule as $col) {
    		//check from date -> to date
    		if($this->checkFromDateToDate($col->getFromDate(), $col->getToDate()) > 0){
				//check website ids
    			$web_id = $col->getWebsiteIds();
 				$web_ids = explode(',',$web_id);
 				$current_webid = Mage::app()->getWebsite()->getId();
 				//sent ticket from frontend of website
				if($fromEmail == 2){
					if(in_array($current_webid, $web_ids)){
						$ret_arr_rule_ids[] = $col->getRuleId();
					}
				}
				//sent ticket from email or update/created ticket from backend
				else {
					$ret_arr_rule_ids[] = $col->getRuleId();
				}
    		}
		}
    	return $ret_arr_rule_ids;//mang chua cac rule_id (chua co check stop further)
    }
    
    //created / updated  va ticket_id
    public function doActionWithTicketWhen($is_created_updated, $ticket_id, $fromEmail){
    	//lay ra cac rule thoa man (chua co dieu stop further)
    	$model_rule = Mage::getModel('helpdesk/rules');
    	$arr_id_rules = $this->getRuleId4Submit($is_created_updated, $fromEmail);
    	$list_id = '';
    	for($i = 0; $i < sizeof($arr_id_rules); $i++) {
    		$model_rule->load($arr_id_rules[$i]);
    		if($model_rule->getStopRulesProcessing() == 1){	
    			//*** importtance: set attributes for ticket id   doActionWithRule(rule_id, ticket_id)
    			$do_action_ticket = $this->doActionWithRule($arr_id_rules[$i], $ticket_id, $fromEmail);
    			$list_id .= $arr_id_rules[$i] . ' - ' . $do_action_ticket . ', ';
    			//break;
    			if($do_action_ticket == 0)	continue;
    			else break;
    		}
    		else {
    			//*** importtance: set attributes for ticket id   doActionWithRule(rule_id, ticket_id)
    			$do_action_ticket = $this->doActionWithRule($arr_id_rules[$i], $ticket_id, $fromEmail);
    			$list_id .= $arr_id_rules[$i] . ' - ' . $do_action_ticket . ', ';
    		}
    	}
    	return $list_id;
    }
    /* ----------------------- ----------------------- ----------------------- */
	
	public function getConditionsInstance()
    {
        return Mage::getModel('helpdesk/helpdeskrule_rule_condition_combine');
    }

	public function _resetConditions($conditions=null)
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        $this->setConditions($conditions);

        return $this;
    }

    public function setConditions($conditions)
    {
        $this->_conditions = $conditions;
        return $this;
    }

    /**
     * Retrieve Condition model
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Abstract
     */

    public function getConditions()
    {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }
        return $this->_conditions;
    }

    public function getActionsInstance()
    {
       return Mage::getModel('helpdesk/helpdeskrule_rule_condition_product_combine');
    }

    public function _resetActions($actions=null)
    {
        if (is_null($actions)) {
            $actions = $this->getActionsInstance();
        }
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        $this->setActions($actions);

        return $this;
    }

    public function setActions($actions)
    {
        $this->_actions = $actions;
        return $this;
    }

    public function getActions()
    {
        if (!$this->_actions) {
            $this->_resetActions();
        }
        return $this->_actions;
    }

    public function getForm()
    {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
        }
        return $this->_form;
    }

    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }

        return $this;
    }

    protected function _convertFlatToRecursive(array $rule)
    {
        $arr = array();
        foreach ($rule as $key=>$value) {
            if (($key==='conditions' || $key==='actions') && is_array($value)) {
                foreach ($value as $id=>$data) {
                    $path = explode('--', $id);
                    $node =& $arr;
                    for ($i=0, $l=sizeof($path); $i<$l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node =& $node[$key][$path[$i]];
                    }
                    foreach ($data as $k=>$v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                /**
                 * convert dates into Zend_Date
                 */
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                        $value,
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );
                }
                $this->setData($key, $value);
            }
        }
        return $arr;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::asArray}
     *   'actions'=>{action_collection::asArray}
     * )
     *
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'name'=>$this->getName(),
            'start_at'=>$this->getStartAt(),
            'expire_at'=>$this->getExpireAt(),
            'description'=>$this->getDescription(),
            'conditions'=>$this->getConditions()->asArray(),
            'actions'=>$this->getActions()->asArray(),
        );

        return $out;
    }

    public function validate(Varien_Object $object)
    {
        return $this->getConditions()->validate($object);
    }
	public function getResourceCollection()
    {
        return Mage::getResourceModel('helpdesk/rules_collection');
    }
    public function afterLoad()
    {
        $this->_afterLoad();
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }

        $actionsArr = unserialize($this->getActionsSerialized());
        if (!empty($actionsArr) && is_array($actionsArr)) {
            $this->getActions()->loadArray($actionsArr);
        }

//        $websiteIds = $this->_getData('website_ids');
//        if (is_string($websiteIds)) {
//            $this->setWebsiteIds(explode(',', $websiteIds));
//        }
//        $groupIds = $this->getCustomerGroupIds();
//        if (is_string($groupIds)) {
//            $this->setCustomerGroupIds(explode(',', $groupIds));
//        }
    }

    /**
     * Prepare data before saving
     *
     * @return Mage_Rule_Model_Rule 
     */
    protected function _beforeSave()
    {
        // check if discount amount > 0
//        if ((int)$this->getDiscountAmount() < 0) {
//            Mage::throwException(Mage::helper('rule')->__('Invalid discount amount.'));
//        }


        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->unsConditions();
        }
        if ($this->getActions()) {
            $this->setActionsSerialized(serialize($this->getActions()->asArray()));
            $this->unsActions();
        }

//        $this->_prepareWebsiteIds();
//
//        if (is_array($this->getCustomerGroupIds())) {
//            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
//        }
        parent::_beforeSave();
    }

    /**
     * Combain website ids to string
     *
     * @return Mage_Rule_Model_Rule
     */
//    protected function _prepareWebsiteIds()
//    {
//        if (is_array($this->getWebsiteIds())) {
//            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
//        }
//        return $this;
//    }

    /**
     * Check availabitlity to delete model
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $flag
     * @return Mage_Rule_Model_Rule
     */
    public function setIsDeleteable($flag)
    {
        $this->_isDeleteable = (bool) $flag;
        return $this;
    }


    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Rule_Model_Rule
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }
    
    /**
     * Validates data for rule
     * @param Varien_Object $object
     * @returns boolean|array - returns true if validation passed successfully. Array with error
     * description otherwise
//     */
//    public function validateData(Varien_Object $object)
//    {
//        if($object->getData('from_date') && $object->getData('to_date')){
//            $dateStartUnixTime = strtotime($object->getData('from_date'));
//            $dateEndUnixTime   = strtotime($object->getData('to_date'));
//
//            if ($dateEndUnixTime < $dateStartUnixTime) {
//                return array(Mage::helper('rule')->__("End Date should be greater than Start Date"));
//            }
//        }
//        return true;
//    }

}
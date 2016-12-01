<?php

class MW_Helpdesk_Model_Observer {

    public function updateSchedule() {
        $xmlPath = Mage::getBaseDir() . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'MW' . DS . 'HelpDesk' . DS . 'etc' . DS . 'config.xml';
        //$xmlPath1 = Mage::getBaseDir() . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'MW' . DS . 'HelpDesk' . DS . 'etc' . DS . 'test.xml';
        
        $domDocument = new DOMDocument();
        $domDocument->load($xmlPath);
        
        $email_minute = intval(Mage::getStoreConfig('helpdesk/config/email_minute'));
        if ($email_minute <= 0) {
            $email_minute = 10;
        }
        $this->updateCronConfig($domDocument, 'get_mail', '<get_mail><schedule><cron_expr>*/'.$email_minute.' * * * *</cron_expr></schedule><run><model>helpdesk/observer::runCron</model></run></get_mail>');
        
        /*
        if (Mage::getStoreConfig('helpdesk/config/delete_ticket_enabled')) {
            $this->updateCronConfig($domDocument, 'delete_expired_ticket', '<delete_ticket_enabled><schedule><cron_expr>0 0 * * *</cron_expr></schedule><run><model>helpdesk/observer::deleteExpiredTicket</model></run></delete_ticket_enabled>');
        } else {
            $this->removeCronConfig($domDocument, 'delete_expired_ticket');
        }
        */
        
        /* remove option Enable Delete Expired Ticket in system helpdesk */
        /* if (Mage::getStoreConfig('helpdesk/config/delete_ticket_enabled')) { */
        if (Mage::getStoreConfig('helpdesk/config/expried_time')>0 && Mage::getStoreConfig('helpdesk/config/expried_time') != '') {
            $this->updateCronConfig($domDocument, 'delete_expired_ticket', '<delete_expired_ticket><schedule><cron_expr>0 0 * * *</cron_expr></schedule><run><model>helpdesk/observer::deleteExpiredTicket</model></run></delete_expired_ticket>');
        } else {
            $this->removeCronConfig($domDocument, 'delete_expired_ticket');
        }

        //delete ticket log
    	if (Mage::getStoreConfig('helpdesk/config/delete_ticketlog')>0 && Mage::getStoreConfig('helpdesk/config/delete_ticketlog') != '') {
            $this->updateCronConfig($domDocument, 'delete_ticket_logs', '<delete_ticket_logs><schedule><cron_expr>0 0 * * *</cron_expr></schedule><run><model>helpdesk/observer::deleteTicketLog</model></run></delete_ticket_logs>');
        } else {
            $this->removeCronConfig($domDocument, 'delete_ticket_logs');
        }
        
        if (intval(Mage::getStoreConfig('helpdesk/config/step_notice')) > 0) {
            $this->updateCronConfig($domDocument, 'notify_missed_ticket', '<notify_missed_ticket><schedule><cron_expr>0 * * * *</cron_expr></schedule><run><model>helpdesk/observer::notifyMissedTicket</model></run></notify_missed_ticket>');
        } else {
            $this->removeCronConfig($domDocument, 'notify_missed_ticket');
        }
        
        $domDocument->save($xmlPath);
        
    }
    
    public function updateCronConfig(&$domDocument ,$cronName, $cronDetail){
        $domXpath = new DOMXPath($domDocument);
        $job = dom_import_simplexml(simplexml_load_string($cronDetail));
        $job = $domDocument->importNode($job, true);
        $path = '//config/crontab/jobs/'.$cronName;
        $domList = $domXpath->query($path);
        if($domList->length > 0){
            $domNode = $domList->item(0);
            $domNode->parentNode->replaceChild($job, $domNode);
        }else{
            $path = '//config/crontab/jobs';
            $domList = $domXpath->query($path);
            $domNode = $domList->item(0);
            $domNode->appendChild($job);
        }
    }
    
    public function removeCronConfig(&$domDocument ,$cronName){
        $domXpath = new DOMXPath($domDocument);
        $path = '//config/crontab/jobs/'.$cronName;
        $domList = $domXpath->query($path);
        if($domList->length > 0){
            $domNode = $domList->item(0);
            $domNode->parentNode->removeChild($domNode);
        }
    }

    public function updateSchedule_1() {
        $email_minute = Mage::getStoreConfig('helpdesk/config/email_minute');
        $value = Mage::getStoreConfig('helpdesk/support_time/timezone');
        Mage::getModel('core/config')->saveConfig('general/locale/timezone', $value);

        $stringData = "<?xml version='1.0'?>
<config>
    <modules>
        <MW_HelpDesk>
            <version>0.1.8</version>
        </MW_HelpDesk>
    </modules>
    <frontend>
        <routers>
            <helpdesk>
                <use>standard</use>
                <args>
                    <module>MW_HelpDesk</module>
                    <frontName>helpdesk</frontName>
                </args>
            </helpdesk>
        </routers>
        <layout>
            <updates>
                <helpdesk>
                    <file>mw_helpdesk.xml</file>
                </helpdesk>
            </updates>
        </layout>
        <translate>
            <modules>
                <MW_Ddate>
                    <files>
                        <default>MW_Helpdesk.csv</default>
                    </files>
                </MW_Ddate>
            </modules>
        </translate>
    </frontend>
    <admin>
        <routers>
			<hdadmin>
				<use>admin</use>
				<args>
					<module>MW_HelpDesk</module>
					<frontName>hdadmin</frontName>
				</args>
			</hdadmin>
        </routers>
        <translate>
            <modules>
                <MW_Ddate>
                    <files>
                        <default>MW_Helpdesk.csv</default>
                    </files>
                </MW_Ddate>
            </modules>
        </translate>
    </admin>
    <adminhtml>
    	<layout>
			<updates>
				<helpdesk>
					<file>mw_helpdesk.xml</file>
				</helpdesk>
			</updates>
		</layout>
    </adminhtml>  
    <global>
        <models>
            <helpdesk>
                <class>MW_HelpDesk_Model</class>
                <resourceModel>helpdesk_mysql4</resourceModel>
            </helpdesk>
            <helpdesk_mysql4>
                <class>MW_HelpDesk_Model_Mysql4</class>
                <entities>
                    <member>
                        <table>mw_members</table>
                    </member>
                    <department>
                        <table>mw_departments</table>
                    </department>
                    <ticket>
                        <table>mw_tickets</table>
                    </ticket>
                    <history>
                    	 <table>mw_histories</table>
                    </history>
                    <deme>
                    	 <table>mw_department_member</table>
                    </deme>
                    <gateway>
                    	 <table>mw_gateways</table>
                    </gateway>
                    <template>
                    	 <table>mw_templates</table>
                    </template>
                    <tag>
                    	 <table>mw_tags</table>
                    </tag>
                    <spam>
                        <table>mw_spam</table>
                    </spam>
                </entities>
            </helpdesk_mysql4>
        </models>
        <resources>
            <helpdesk_setup>
                <setup>
                    <module>MW_HelpDesk</module>
                </setup>
                <connection>
                    <use>core_setup</use>
                </connection>
            </helpdesk_setup>
            <helpdesk_write>
                <connection>
                    <use>core_write</use>
                </connection>
            </helpdesk_write>
            <helpdesk_read>
                <connection>
                    <use>core_read</use>
                </connection>
            </helpdesk_read>
        </resources>
        <blocks>
            <helpdesk>
                <class>MW_HelpDesk_Block</class>
            </helpdesk>
        </blocks>
        <helpers>
            <helpdesk>
                <class>MW_HelpDesk_Helper</class>
            </helpdesk>
        </helpers>
        <events>
        	<admin_system_config_changed_section_helpdesk>
                <observers>
                    <update_time>
                        <type>singleton</type>
                        <class>MW_HelpDesk_Model_Observer</class>
                        <method>updateSchedule</method>
                    </update_time>
                </observers>
            </admin_system_config_changed_section_helpdesk>
			
			<controller_front_init_before>
            	<observers>
                    <helpdesk>
                        <type>singleton</type>
                        <class>helpdesk/observer</class>
                        <method>checkLicense</method>
                    </helpdesk>
                </observers>
			</controller_front_init_before> 


    	</events>
    	<!--<rewrite>
        	<mw_credit_adminhtml_sales_order_invoice>
        		<from><![CDATA[#^/contacts/index/#]]></from>
                <to>/helpdesk/contacts_index/</to>
        	</mw_credit_adminhtml_sales_order_invoice>
        </rewrite>-->" .
                '<template>
		  	<email>
		    	<helpdesk_helpdesk_email_temp_new_ticket_customer translate="label" module="helpdesk">
			      	<label>Client New Ticket Template</label>
			      	<file>mw_helpdesk/client_new_ticket_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_new_ticket_customer>
		    	<helpdesk_helpdesk_email_temp_reply_ticket_customer translate="label" module="helpdesk">
			      	<label>Staff Response Template</label>
			      	<file>mw_helpdesk/staff_reply_ticket_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_reply_ticket_customer>
		    	<helpdesk_helpdesk_email_temp_new_ticket_operator translate="label" module="helpdesk">
			      	<label>Staff New Ticket Template</label>
			      	<file>mw_helpdesk/staff_new_ticket_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_new_ticket_operator>
		    	<helpdesk_helpdesk_email_temp_reply_ticket_operator translate="label" module="helpdesk">
			      	<label>Client Response Template</label>
			      	<file>mw_helpdesk/client_reply_ticket_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_reply_ticket_operator>
		    	<helpdesk_helpdesk_email_temp_reassign_ticket_operator translate="label" module="helpdesk">
			      	<label>Ticket Reassign Template</label>
			      	<file>mw_helpdesk/ticket_reassign_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_reassign_ticket_operator>
		    	<helpdesk_helpdesk_email_temp_late_reply_ticket_operator translate="label" module="helpdesk">
			      	<label>Late Response Template</label>
			      	<file>mw_helpdesk/late_reply_ticket_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_late_reply_ticket_operator>
		    	<helpdesk_helpdesk_email_temp_internal_note_notification translate="label" module="helpdesk">
			      	<label>Internal Note Notification Template</label>
			      	<file>mw_helpdesk/internal_note_notification_template.html</file>
			      	<type>html</type>
		    	</helpdesk_helpdesk_email_temp_internal_note_notification>
		  	</email>
		</template>' .
                "</global>
    <default>
		<helpdesk>
			<config>
                <enabled>1</enabled>
                <email_minute>10</email_minute>
                <notice>24</notice>
                <complete>24</complete>
               	<expried_time>90</expried_time>
           	</config>
           	
           	<support_time>
                <support>0</support>
            </support_time>
            
            <client_config>
                <department>1</department>
				<default_sender>support@mage-world.com</default_sender>
                <priority>1</priority>
                <order>0</order>
                <upload>1</upload>
			
                <max_upload>3</max_upload>
            </client_config>
           
			<email_config>
                <creticket_email>1</creticket_email>
                <contact>0</contact>
                <tag_html>strong;i;span;p;u;b;li;ol;ul;big;br</tag_html>
			</email_config>
		</helpdesk>
	</default>
    <crontab>
        <jobs>
            <get_mail>
                <schedule><cron_expr>*/$email_minute * * * *</cron_expr></schedule>
                <run><model>helpdesk/observer::runCron</model></run>
            </get_mail>
        </jobs>
    </crontab>";
        //if (Mage::getStoreConfig('helpdesk/config/delete_ticket_enabled')) {
        if (Mage::getStoreConfig('helpdesk/config/expried_time')>0 && Mage::getStoreConfig('helpdesk/config/expried_time') != '') {
            $stringData .= "
            <crontab>
                <jobs>
                    <delete_expired_ticket>
                        <schedule>
                            <cron_expr>0 0 * * *</cron_expr>
                        </schedule>
                        <run>
                            <model>helpdesk/observer::deleteExpiredTicket</model>
                        </run>
                    </delete_expired_ticket>
                </jobs>
            </crontab>";
        }
        
    	if (Mage::getStoreConfig('helpdesk/config/delete_ticketlog')>0 && Mage::getStoreConfig('helpdesk/config/delete_ticketlog') != '') {
            $stringData .= "
            <crontab>
                <jobs>
                    <delete_ticket_logs>
                        <schedule>
                            <cron_expr>0 0 * * *</cron_expr>
                        </schedule>
                        <run>
                            <model>helpdesk/observer::deleteTicketLog</model>
                        </run>
                    </delete_ticket_logs>
                </jobs>
            </crontab>";
        }
        
        $noticeTime = Mage::getStoreConfig('helpdesk/config/step_notice');
        if (intval($noticeTime) > 0) {
            $stringData .= "
                <crontab>
                    <jobs>
                        <notify_missed_ticket>
                            <schedule>
                                <cron_expr>0 * * * *</cron_expr>
                            </schedule>
                            <run>
                                <model>helpdesk/observer::notifyMissedTicket</model>
                            </run>
                        </notify_missed_ticket>
                    </jobs>
                </crontab>";
        }
        $stringData .= "</config>";

        // $_SERVER['SCRIPT_NAME']; $_SERVER['PHP_SELF']; $_SERVER['DOCUMENT_ROOT']; $_SERVER['SCRIPT_FILENAME'];
        $path = getcwd() . "/app/code/local/MW/HelpDesk/etc/config.xml";
        $fh = fopen($path, 'w') or die("can't open file");
        fwrite($fh, $stringData);
        fclose($fh);
    }

    public function runCron() {
        Mage::getModel('helpdesk/email')->runCron();
    }

    public function deleteExpiredTicket() {
        //if (is_numeric(Mage::getStoreConfig('helpdesk/config/expried_time'))) {
    	if (Mage::getStoreConfig('helpdesk/config/expried_time')>0 && Mage::getStoreConfig('helpdesk/config/expried_time') != '') {
            Mage::getModel('helpdesk/ticket')->deleteExpiredTicket();
        }
    }
    
	public function deleteTicketLog() {
		if (Mage::getStoreConfig('helpdesk/config/delete_ticketlog')>0 && Mage::getStoreConfig('helpdesk/config/delete_ticketlog') != '') {
            Mage::getModel('helpdesk/ticketlog')->deleteTicketLog();
        }
    }

    public function notifyMissedTicket() {
        Mage::getModel('helpdesk/email')->generateNotice();
    }
	
	//when click ticket grid -> update time
	public function clickTicketGrid($observer) {
    	$model = Mage::getModel('helpdesk/ticket');
    	if($observer->getTicketId() != ''){
	      	$model->setStaffWorkingTime(now())->setId($observer->getTicketId()); 
	        $model->save();
    	}
	}
	
	//auto set null staff working time
	public function autoSetNullStaffWorkingTime() {
		$model = Mage::getModel('helpdesk/ticket');
		$collections = $model->getCollection();
		$date_now = Mage::getSingleton('core/date')->timestamp(time());
		foreach ($collections as $value) {
	    	if($value->getStaffWorkingTime() != ''){
				$date_register = Mage::getSingleton('core/date')->timestamp($value->getStaffWorkingTime());
				if (($date_now - $date_register) > 15){
			      	$model->setStaffWorkingTime(null)->setId($value->getId()); 
			        $model->save();
				}
	    	}
		}
	}
	public function checkLicense($o)
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled')!=1)
		{
			Mage::helper('helpdesk')->disableConfig();
		}
		
	}

	
}
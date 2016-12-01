<?php

class MW_HelpDesk_Block_Adminhtml_Gateway_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('helpdesk_form', array('legend' => Mage::helper('helpdesk')->__('Gateway information')));
		
		//echo 'URl: ' . $base_url. ' = ' . Mage::getStoreConfig('web/url/use_store') . '<br />';
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Gateway Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
        
        $fieldset->addField('sender_name', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Sender Name'),
            'note' => 'Use sender name when sending notification',
            'name' => 'sender_name',
        ));

        $fieldset->addField('active', 'select', array(
            'label' => Mage::helper('helpdesk')->__('Active'),
            'name' => 'active',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('helpdesk')->__('Yes'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('helpdesk')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('host', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Gateway Host'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'host',
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Gateway Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
            'name' => 'email',
        ));

        $fieldset->addField('login', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Login'),
            'name' => 'login',
        ));

        $fieldset->addField('password', 'password', array(
            'label' => Mage::helper('helpdesk')->__('Password'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'password',
        ));

        $fieldset->addField('port', 'text', array(
            'label' => Mage::helper('helpdesk')->__('Port'),
            'name' => 'port',
        ));

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('helpdesk')->__('Type'),
            'name' => 'type',
            'options' => array(
                '1' => Mage::helper('helpdesk')->__('IMAP'),
                '2' => Mage::helper('helpdesk')->__('POP3'))
        ));

        $fieldset->addField('ssl', 'select', array(
            'label' => Mage::helper('helpdesk')->__('Use SSL/TLS'),
            'name' => 'ssl',
            'options' => array(
                '1' => Mage::helper('helpdesk')->__('Yes'),
                '2' => Mage::helper('helpdesk')->__('No'))
        ));

        $departments = array();
        $departments[''] = '-- Please select Department --';
        $collection = Mage::getModel('helpdesk/department')->getCollection()
                ->addFieldToFilter('active', array('eq' => 1));
        foreach ($collection as $department) {
            $departments[$department->getId()] = $department->getName();
        }
        $fieldset->addField('default_department', 'select', array(
            'label' => Mage::helper('helpdesk')->__('Default Department'),
            'name' => 'default_department',
            'values' => $departments,
        ));


        $fieldset->addField('delete_email', 'select', array(
            'label' => Mage::helper('helpdesk')->__('Delete Email From Host'),
            'name' => 'delete_email',
            'note' => 'Applied only with IMAP (If you choose POP3, mails are auto-deleted from host after these mails were transfered to ticket system)',
            'options' => array(
                '1' => Mage::helper('helpdesk')->__('Yes'),
                '2' => Mage::helper('helpdesk')->__('No'))
        ));

        $testButton = $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(array(
            'id' => 'testGateway',
            'label' => Mage::helper('helpdesk')->__('Test Connect'),
            'class' => 'save'
                ));
        $fieldset->addField('test_button', 'note', array(
            'text' => $testButton->toHtml(),
        ));

        if (Mage::getSingleton('adminhtml/session')->getGatewayData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGatewayData());
            Mage::getSingleton('adminhtml/session')->setGatewayData(null);
        } elseif (Mage::registry('gateway_data')) {
            $form->setValues(Mage::registry('gateway_data')->getData());
        }
        return parent::_prepareForm();
    }

}
?>
<script type="text/javascript">
    //<![CDATA[
    document.observe("dom:loaded", function() {
        var baseurl = '<?php echo Mage::getBaseUrl('skin'); ?>';
		<?php 
			/* fix use store url */
			$helper = Mage::helper('helpdesk/data');
			if(Mage::getStoreConfig('web/url/use_store') == 1){
				$base_url = $helper->_getDefaultBaseUrl(). 'helpdesk/gateway/test/';
			}
			else{
				$base_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'helpdesk/gateway/test/';
			}
		?>
       // $$('body')[0].insert('<div style="display: none;" id="my-loading-mask"><div id="loading-mask"><p id="my-loading_mask_loader" class="loader"><img alt="Loading..." src="'+baseurl+'adminhtml/default/default/images/ajax-loader-tr.gif"><br>Please wait...</p></div></div>');
        Event.observe('testGateway','click', function(){
            var loadUrl = "<?php echo $base_url; //Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'helpdesk/gateway/test/'; ?>";
            var host = $F('host');
            var email = $F("email");
            var login = $F("login");
            var password = $F("password");
            var port = $F("port");
            var type = $F("type");
            var ssl = $F("ssl");
           // Effect.Appear('my-loading-mask');
            new Ajax.Request(loadUrl, {
                method: 'post',
                parameters: {
                    host: host, 
                    email: email, 
                    login: login, 
                    password: password, 
                    port: port, 
                    type: type, 
                    ssl: ssl},
                onSuccess: function (response){
           //         Effect.Fade('my-loading-mask');
                    alert(response.responseText);
                }
            });
        }); 
    });


    //	$j_mw(document).ready(function() {
    //		var baseurl = '<?php //echo Mage::getBaseUrl('skin');   ?>';
    //		$j_mw('body').append('<div style="display: none;" id="my-loading-mask"><div id="loading-mask"><p id="my-loading_mask_loader" class="loader"><img alt="Loading..." src="'+baseurl+'adminhtml/default/default/images/ajax-loader-tr.gif"><br>Please wait...</p></div></div>');
    //		
    //  		$j_mw('#testGateway').click(function() { 
    //  			var loadUrl 	= '<?php echo Mage::helper('adminhtml')->getUrl('helpdesk/gateway/test'); ?>';
    //			var host 		= $j_mw("#host").val()
    //    		var email 		= $j_mw("#email").val()
    //    		var login 		= $j_mw("#login").val()
    //    		var password 	= $j_mw("#password").val()
    //    		var port 		= $j_mw("#port").val()
    //    		var type 		= $j_mw("#type").val()
    //    		var ssl 		= $j_mw("#ssl").val()
    //			
    //			// ajax
    //			$j_mw("#my-loading-mask").show(2000);
    //			//$j_mw("#loading-mask").hide("slow");
    //			$j_mw.get(loadUrl,
    //  					{host: host, email: email, login: login, 
    //					 password: password, port: port, type: type, ssl: ssl},
    //  					function(responseText){
    //						$j_mw("#my-loading-mask").hide('slow', function() {
    //							    alert(responseText);
    //						});
    //						//alert(responseText);
    //						
    //  					},
    //  					"html"
    //  			);
    //  		});
    //	});
    //]]>
</script>
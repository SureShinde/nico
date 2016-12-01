Event.observe(window, 'load', function() { 
	if($('helpdesk_client_config_department').value == 1){
		$('helpdesk_client_config_default_department').value = "";
		$('helpdesk_client_config_default_department').disable();
	}
	else{
		$('helpdesk_client_config_default_department').enable();
	}
	
	Event.observe('helpdesk_client_config_department', 'change', function(event) {
		if($('helpdesk_client_config_department').value == 1){
			$('helpdesk_client_config_default_department').value = "";
			$('helpdesk_client_config_default_department').disable();
		}
		else{
			$('helpdesk_client_config_default_department').enable();
			alert('Please select Default Support Department under Customer Configurations tab');
		}
	});
});
Event.observe(window, 'load', function() {
	//for load event
	if($('status_new_ticket_customer').value == 1)
	{
		$('new_ticket_customer').show();
		$('new_ticket_customer').disable();
	}
	else if($('status_new_ticket_customer').value == 3)
		$('new_ticket_customer').hide();
	else{ 
		$('new_ticket_customer').show();
		$('new_ticket_customer').enable();
	}

	if($('status_reply_ticket_customer').value == 1)
	{
		$('reply_ticket_customer').show();
		$('reply_ticket_customer').disable();
	}
	else if($('status_reply_ticket_customer').value == 3)
		$('reply_ticket_customer').hide();
	else{ 
		$('reply_ticket_customer').show();
		$('reply_ticket_customer').enable();
	}
	if($('status_new_ticket_operator').value == 1)
	{
		$('new_ticket_operator').show();
		$('new_ticket_operator').disable();
	}
	else if($('status_new_ticket_operator').value == 3)
		$('new_ticket_operator').hide();
	else{ 
		$('new_ticket_operator').show();
		$('new_ticket_operator').enable();
	}

	if($('status_reply_ticket_operator').value == 1)
	{
		$('reply_ticket_operator').show();
		$('reply_ticket_operator').disable();
	}
	else if($('status_reply_ticket_operator').value == 3)
		$('reply_ticket_operator').hide();
	else{ 
		$('reply_ticket_operator').show();
		$('reply_ticket_operator').enable();
	}

	if($('status_reassign_ticket_operator').value == 1)
	{
		$('reassign_ticket_operator').show();
		$('reassign_ticket_operator').disable();
	}
	else if($('status_reassign_ticket_operator').value == 3)
		$('reassign_ticket_operator').hide();
	else{ 
		$('reassign_ticket_operator').show();
		$('reassign_ticket_operator').enable();
	}

	if($('status_late_reply_ticket_operator').value == 1)
	{
		$('late_reply_ticket_operator').show();
		$('late_reply_ticket_operator').disable();
	}
	else if($('status_late_reply_ticket_operator').value == 3)
		$('late_reply_ticket_operator').hide();
	else{ 
		$('late_reply_ticket_operator').show();
		$('late_reply_ticket_operator').enable();
	}

	if($('status_internal_note_notification').value == 1)
	{
		$('internal_note_notification').show();
		$('internal_note_notification').disable();
	}
	else if($('status_internal_note_notification').value == 3)
		$('internal_note_notification').hide();
	else{ 
		$('internal_note_notification').show();
		$('internal_note_notification').enable();
	}

	
	//for click
	Event.observe('status_new_ticket_customer', 'click', function(event) {
		if($('status_new_ticket_customer').value == 1)
		{
			$('new_ticket_customer').show();
			$('new_ticket_customer').disable();
		}
		else if($('status_new_ticket_customer').value == 3)
			$('new_ticket_customer').hide();
		else{ 
			$('new_ticket_customer').show();
			$('new_ticket_customer').enable();
		}
	});
	
	Event.observe('status_reply_ticket_customer', 'click', function(event) {
		if($('status_reply_ticket_customer').value == 1)
		{
			$('reply_ticket_customer').show();
			$('reply_ticket_customer').disable();
		}
		else if($('status_reply_ticket_customer').value == 3)
			$('reply_ticket_customer').hide();
		else{ 
			$('reply_ticket_customer').show();
			$('reply_ticket_customer').enable();
		}
	});
	
	Event.observe('status_new_ticket_operator', 'click', function(event) {
		if($('status_new_ticket_operator').value == 1)
		{
			$('new_ticket_operator').show();
			$('new_ticket_operator').disable();
		}
		else if($('status_new_ticket_operator').value == 3)
			$('new_ticket_operator').hide();
		else{ 
			$('new_ticket_operator').show();
			$('new_ticket_operator').enable();
		}
	});
	
	Event.observe('status_reply_ticket_operator', 'click', function(event) {
		if($('status_reply_ticket_operator').value == 1)
		{
			$('reply_ticket_operator').show();
			$('reply_ticket_operator').disable();
		}
		else if($('status_reply_ticket_operator').value == 3)
			$('reply_ticket_operator').hide();
		else{ 
			$('reply_ticket_operator').show();
			$('reply_ticket_operator').enable();
		}
	});
	
	Event.observe('status_reassign_ticket_operator', 'click', function(event) {
		if($('status_reassign_ticket_operator').value == 1)
		{
			$('reassign_ticket_operator').show();
			$('reassign_ticket_operator').disable();
		}
		else if($('status_reassign_ticket_operator').value == 3)
			$('reassign_ticket_operator').hide();
		else{ 
			$('reassign_ticket_operator').show();
			$('reassign_ticket_operator').enable();
		}
	});
	
	Event.observe('status_late_reply_ticket_operator', 'click', function(event) {
		if($('status_late_reply_ticket_operator').value == 1)
		{
			$('late_reply_ticket_operator').show();
			$('late_reply_ticket_operator').disable();
		}
		else if($('status_late_reply_ticket_operator').value == 3)
			$('late_reply_ticket_operator').hide();
		else{ 
			$('late_reply_ticket_operator').show();
			$('late_reply_ticket_operator').enable();
		}
	});
	
	Event.observe('status_internal_note_notification', 'click', function(event) {
		if($('status_internal_note_notification').value == 1)
		{
			$('internal_note_notification').show();
			$('internal_note_notification').disable();
		}
		else if($('status_internal_note_notification').value == 3)
			$('internal_note_notification').hide();
		else{ 
			$('internal_note_notification').show();
			$('internal_note_notification').enable();
		}
	});
	
});
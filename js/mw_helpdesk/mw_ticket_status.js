Event.observe(window, 'load', function() {
	////////////////////// load ///////////////////////////
	var options = $$('select#status option');
	var len = options.length;
	options[0].hide();
	options[1].selected=true;
	///////////////////////

	Event.observe('status', 'click', function(event) {
		var options = $$('select#status option');
		var len = options.length;
		options[0].hide();
	});
	
});
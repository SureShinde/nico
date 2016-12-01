Event.observe(window, 'load', function() {
	////////////////////// load ///////////////////////////
	var options = $$('select#update_order option');
	var len = options.length;
	
	for (var i = 0; i < len; i++) {
		if(options[i].selected){
			if(options[i].value != '0'){
				$('update_order1').hide();
			}
			else { $('update_order1').show(); break;}
		}
	}
	///////////////////////

	Event.observe('update_order', 'click', function(event) {
		var options = $$('select#update_order option');
		var len = options.length;

		for (var i = 0; i < len; i++) {
			if(options[i].selected){
				if(options[i].value != '0'){
					$('update_order1').hide();
				}
				else { $('update_order1').show(); break;}
			}
		}
	});
});
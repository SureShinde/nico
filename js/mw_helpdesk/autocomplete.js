var $j_mw = jQuery.noConflict();
$j_mw(function(){
	var baseUrl=$j_mw("#url").val();
	var moderatorarr=[];
	$j_mw.ajax({
		type:"POST",url:baseUrl+"helpdesk/autocomplete/operator",
		complete:function(data){moderatorarr=eval('('+data.responseText+')');}
    });
	setTimeout(ac,1000);	
	function ac(){
		$j_mw("#moderator").autocomplete({
			source:moderatorarr
		});
		$j_mw("#member_id").autocomplete({
			source:moderatorarr
		});
		$j_mw("#operator").autocomplete({
			source:moderatorarr
		});
	}
	
	$j_mw( "#sender" ).autocomplete({
		source: baseUrl + "helpdesk/autocomplete/client",
		minLength: 2
	});
	
	var email =  '';
	$j_mw('input[name=sender]').blur(function() { 
		email = $j_mw("#sender").val();
	});
	$j_mw('#order').click(function() {
		$j_mw( "#order" ).autocomplete({
			source: baseUrl + "helpdesk/autocomplete/order/email/"+email,
			minLength: 2
		});
	});

	
});
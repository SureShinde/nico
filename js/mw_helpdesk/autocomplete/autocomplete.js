var $j_mw = jQuery.noConflict();
var focused=0;
$j_mw(function(){
	// operator & tag
	var baseUrl=$j_mw("#url").val();
	var moderatorarr=[];
	var tagsarr=[];
	var moderatorDistinctarr=[];
	var templatearr=[];
	var categoryarr=[];
	$j_mw.ajax({
		type:"POST",url:baseUrl+"helpdesk/autocomplete/operator",
		complete:function(data){
			moderatorarr=eval('('+data.responseText+')');			
		}
    });
    
    	$j_mw.ajax({
		type:"POST",url:baseUrl+"helpdesk/autocomplete/template",
		complete:function(data){
			templatearr = eval('('+data.responseText+')');			
		}
    });
    	
	$j_mw.ajax({
		type:"POST",url:baseUrl+"helpdesk/autocomplete/operatorDistinct",
		complete:function(data){
			moderatorDistinctarr=eval('('+data.responseText+')');			
		}
    });
	$j_mw.ajax({
		type:"POST",url:baseUrl+"helpdesk/autocomplete/tags",
		complete:function(data){
			tagsarr=eval('('+data.responseText+')');
		}
    });
	$j_mw("#moderator").attr('disabled', 'disabled');
	$j_mw("#tags").attr('disabled', 'disabled');
	setTimeout(ac,2000);	
	function ac(){
		$j_mw("#moderator").removeAttr('disabled');
		$j_mw("#tags").removeAttr('disabled');
		$j_mw("#moderator").autocomplete(moderatorDistinctarr, {
			matchContains: "word",
			formatItem: function(row) {
				return row.name + " [" + row.email + "]";
			},
			formatResult: function(row) {
				return row.email;
			}
		});
//		$j_mw("#member_id").autocomplete(moderatorarr, {
//			matchContains: "word",
//			formatItem: function(row) {
//				return row.name + " [" + row.email + "]";
//			},
//			formatResult: function(row) {
//				return row.email;
//			}
//		});
//		$j_mw("#operator").autocomplete(moderatorarr, {
//			matchContains: "word",
//			formatItem: function(row) {
//				return row.name + " [" + row.email + "]";
//			},
//			formatResult: function(row) {
//				return row.email;
//			}
//		});
		$j_mw("#tags").autocomplete(tagsarr, {
			multiple: true,
			matchContains: true,
			autoFill: true
		});
	}
	
	$j_mw('#department_id').change(function() {
		if($j_mw("#moderator").val() == '') {
			// xoa list neu ton tai
			liop = document.getElementById('moderator_list');
			if (liop != null) liop.parentNode.removeChild(liop);
			
			// neu chon department
			var data = [], m = document.getElementById("moderator"), fix=0;
			if($j_mw("#department_id").val() != '') {
				for(var i=0;i<moderatorarr.length;i++){
					if(moderatorarr[i]['department_name'] == $j_mw("#department_id :selected").text()) {
						data[fix] = moderatorarr[i]['name']+' ['+moderatorarr[i]['email'] + ']';
						fix++;
					}
				}
				var select = createList(data);
			    m.parentNode.insertBefore(select, m);
				m.style.display='none';
			} else {
				m.style.display='';
			}
		}
	});
	
	//set new for status when create ticket
	$j_mw(document).ready(function() {
		document.getElementById('status').value = 4;
    	});
	
	//allow select response template when create ticket
	$j_mw('#template_id').change(function() {
		var m = document.getElementById("_content");
		if($j_mw("#template_id").val() != '') {
			for(var i=0;i<templatearr.length;i++){
				if(templatearr[i]['title'] == $j_mw("#template_id :selected").text()) {
					try {
						tinyMCE.activeEditor.setContent(templatearr[i]['message']);
					}
					catch(err){
						m.value = templatearr[i]['message'];
					}
				}
			}
		}
    	});
	
	$('id_category').observe('change', function(){
	    new Ajax.Request(baseUrl+'helpdesk/viewticket/categoryresponse', {
	        method: 'get',
	        parameters : {id_cate: this.getValue()},
	        onSuccess: function(response){
	        	//document.getElementById('loading-mask2').innerHTML ="";	 
				categoryarr=eval('('+response.responseText+')');
	
				var elSel = document.getElementById('template_id');
				var i;
				for (i = elSel.length - 1; i>=1; i--) {
				     elSel.remove(i);
				}
	
				var m = document.getElementById('template_id');
				var select = document.createElement('select');
				select.id = 'template_id';
				select.name = 'template_id';
	
				for(var i=0;i<categoryarr.length;i++){
				    var option = document.createElement("option");
				    option.value=categoryarr[i]['template_id'];
				    var newText = document.createTextNode(categoryarr[i]['title']);
				    option.appendChild(newText);
				    m.appendChild(option);
				}
				//m.parentNode.insertBefore(select, m);
				//m.style.display='none';
	
	        }});
	    ;
	});
	
	function createList(data) {
	  var select = document.createElement("select");
	  select.id = 'moderator_list';
	  select.name = 'moderator_list';
	  for (var i = 0; i < data.length; i++) {
	    var option = document.createElement("option");
		var p = data[i].split("[")[1];
	    option.value=p.split("]")[0];
	    var newText = document.createTextNode(data[i]);
	    option.appendChild(newText);
	    select.appendChild(option);
	  }
	  return select;
	}
	// client
	var clienturl=$j_mw("#clienturl").val();
	$j_mw("#sender").autocomplete(clienturl,{
		formatItem: function(data, i, n, value) {
			return value;
		},
		formatResult: function(data, value) {
			var p = value.split("[")[1];
			return p.split("]")[0];
		}
	});
	
	document.getElementById('sender').onfocus=function() {
        focused=1;
    };
	document.getElementById('moderator').onfocus=function() {
        focused=0;
    };
	document.getElementById('order').onfocus=function() {
        focused=0;
    };
	document.getElementById('tags').onfocus=function() {
        focused=0;
    };
	// name & order 
	var orderurl=$j_mw("#orderurl").val();
	$j_mw('#order').click(function() {
		var email = $j_mw("#sender").val();
		if(email != '') {
			var url=orderurl+"email/"+email;
		} else {
			var url=orderurl;
		}	
		$j_mw("#order").autocomplete(url,{
			matchContains: "word",
			formatItem: function(data, i, n, value) {
				return value;
			},
			formatResult: function(data, value) {
				p = value.split("(")[0];
				return p.split("#")[1];;
			}
		});
	});


});
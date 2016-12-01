jQuery(document).ready(function() {				
	// TAB ONE
	jQuery(document).on("click", ".order_one", function(e){	
		jQuery(".order_one_container").show();
		jQuery(this).addClass("active");
		jQuery(".order_multiple_container").hide();
		jQuery(".order_multiple").removeClass("active");	
	});

	// TAB MULTIPLE
	jQuery(document).on("click", ".order_multiple", function(e){	
		jQuery(".order_multiple_container").show();
		jQuery(this).addClass("active");
		jQuery(".order_one_container").hide();
		jQuery(".order_one").removeClass("active");		
	});
	
	// ONE FIRST CLICK
	jQuery(document).on("click", "[id^=one_first_]", function(e){						
		var id = jQuery(this).attr("id").match(/\d+/)[0];
		
		jQuery("[id^=one_first_]").removeClass("active");
		jQuery("[id^=one_first_]").removeClass("selected");
		jQuery(this).addClass("active");
		jQuery(this).addClass("selected");

		if(jQuery("[id^=one_second_]").length > 0){
			jQuery("[id^=one_second_]").removeClass("active");
			jQuery("[id^=one_second_]").removeClass("selected");
		    jQuery("[id^=one_second_]").each(function(index) {
		    	var options = jQuery(this).attr("data-options").split(',');
		    	if(jQuery.inArray(id, options) > -1){  
		    		jQuery(this).removeClass("disabled");
	    		}else{
		    		jQuery(this).addClass("disabled");
	    		}
			});	
		    
		    if(jQuery("[id^=one_third_]").length > 0){
		    	jQuery("[id^=one_third_]").removeClass("active");
				jQuery("[id^=one_third_]").removeClass("selected");
				jQuery("[id^=one_third_]").addClass("disabled");
		    }
		    
		    jQuery("#order_one_qty").val(0);
		    jQuery("#order_one_qty").css("border-color", "#B6B6B6");
			jQuery("#order_one_qty").attr("disabled", "disabled");
		}else if(jQuery("[id^=one_third_]").length > 0){
			jQuery("[id^=one_third_]").removeClass("active");
			jQuery("[id^=one_third_]").removeClass("selected");
		    jQuery("[id^=one_third_]").each(function(index) {
		    	var options = jQuery(this).attr("data-options").split(',');
		    	if(jQuery.inArray(id, options) > -1){  
		    		jQuery(this).removeClass("disabled");
	    		}else{
		    		jQuery(this).addClass("disabled");
	    		}
			});	
		
		    jQuery("#order_one_qty").val(0);
		    jQuery("#order_one_qty").css("border-color", "#B6B6B6");
			jQuery("#order_one_qty").attr("disabled", "disabled");
		}else{
			jQuery("#order_one_qty").removeAttr("disabled");
			jQuery("#order_one_qty").css("border-color", "#B6B6B6");
			jQuery("#order_one_qty").val(1);
		}	
		
		if(jQuery(".order_one_common_popup").is(":visible")){
			jQuery(".order_one_common_popup ").hide();
		}
	});
		
	// ONE SECOND CLICK
	jQuery(document).on("click", "[id^=one_second_]", function(e){
		var id = jQuery(this).attr("id").match(/\d+/)[0];
		if(!jQuery(this).hasClass("disabled")){
			jQuery("[id^=one_second_]").removeClass("active");
			jQuery("[id^=one_second_]").removeClass("selected");
			jQuery(this).addClass("active");
			jQuery(this).addClass("selected");
		
			if(jQuery("[id^=one_third_]").length > 0){
				jQuery("[id^=one_third_]").removeClass("active");
				jQuery("[id^=one_third_]").removeClass("selected");
				
				if(jQuery("[id^=one_first_]").length > 0){
					jQuery("[id^=one_first_]").each(function(index) {
						if(jQuery(this).hasClass("active")){
							id = jQuery(this).attr("id").match(/\d+/)[0] + "-" + id;
							return false;
						}
					});
				}
				
			    jQuery("[id^=one_third_]").each(function(index) {
			    	var options = jQuery(this).attr("data-options").split(',');
			    	if(jQuery.inArray(id, options) > -1){  
			    		jQuery(this).removeClass("disabled");
		    		}else{
			    		jQuery(this).addClass("disabled");
		    		}
				});	
			
			    jQuery("#order_one_qty").val(0);
			    jQuery("#order_one_qty").css("border-color", "#B6B6B6");
				jQuery("#order_one_qty").attr("disabled", "disabled");
			}else{
				jQuery("#order_one_qty").removeAttr("disabled");
				jQuery("#order_one_qty").css("border-color", "#B6B6B6");
				jQuery("#order_one_qty").val(1);
			}		
		}
		
		if(jQuery(".order_one_common_popup").is(":visible")){
			jQuery(".order_one_common_popup ").hide();
		}
	});
		
	// ONE THIRD CLICK
	jQuery(document).on("click", "[id^=one_third_]", function(e){
		var id = jQuery(this).attr("id").match(/\d+/)[0];
		if(!jQuery(this).hasClass("disabled")){
			jQuery("[id^=one_third_]").removeClass("active");
			jQuery("[id^=one_third_]").removeClass("selected");
			jQuery(this).addClass("active");
			jQuery(this).addClass("selected");
	
			jQuery("#order_one_qty").removeAttr("disabled");
			jQuery("#order_one_qty").css("border-color", "#B6B6B6");
			jQuery("#order_one_qty").val(1);		
		}
		
		if(jQuery(".order_one_common_popup").is(":visible")){
			jQuery(".order_one_common_popup ").hide();
		}
	});

	// ONE QUANTITY FOCUS
	jQuery(document).on("focusin", "#order_one_qty", function(e){
		if(jQuery(this).val() == 0){
			jQuery(this).val('');
		}
	}).on("focusout", "#order_one_qty", function(e){
		if(jQuery(this).val() == ''){
			jQuery(this).val(0);
			jQuery(this).css("border-color", "#B6B6B6");
			if(jQuery(".order_one_common_popup").is(":visible")){
				jQuery(".order_one_common_popup ").hide();
			}
		}
	});
	
	//CHECK ONE QUANTITY(KEYUP)
	var timeout = null;
	jQuery(document).on("keyup", "#order_one_qty", function(e){	
		var base_url = jQuery("#base_url").val();
		var position = jQuery(this).position();
		var that = jQuery(this);
		
		var product_id = jQuery("#product_id").val();
		var attributes = {};
		var qty = jQuery(this).val();
		
		var unselected_attribute = "";
		jQuery.each(["first", "second", "third"], function(index, value){
			if(jQuery("#order_one_" + value + "_super_attribute").length > 0){
				var attribute = 0;
				jQuery("[id^=one_" + value + "_]").each(function(index) {
					if(jQuery(this).hasClass("selected")){
						attribute = jQuery(this).attr("id").match(/\d+/)[0];			
					}
				});
				
				if(attribute != 0){
					attributes[jQuery("#order_one_" + value + "_super_attribute").val()] = attribute;
				}else{
					unselected_attribute = value;
					return false;
				}
			}
		});
	
		if(unselected_attribute != ''){
			  jQuery(this).css("border-color", "#D3441C");
			  var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
				           "position_top":parseInt(position.top) - 9, "margin_top":"6px", "message":Translator.translate("Please select a " + unselected_attribute + " attribute."), "timeout":"", "display":"false"}];
			  showMessage(data);
			  return false;
		}else if(!jQuery.isNumeric(qty) || qty <= 0){
			  jQuery(this).css("border-color", "#D3441C");
			  var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
		                   "position_top":parseInt(position.top) - 9, "margin_top":"0", "message":Translator.translate("Invalid input for quantity. Please insert positive number."), "timeout":"", "display":"false"}];
			  showMessage(data);
			  return false;
		}
		
		if(timeout != null) {
			clearTimeout(timeout);
        }
		
		timeout = setTimeout(function(){   
			showLoader();
			jQuery.ajax({			
		        url: base_url + "quickorder/ajax/checkInvetory",
		        type: "POST",
		        data: "product_id=" + product_id + "&attributes=" + JSON.stringify(attributes) + "&qty=" + qty,
		        dataType: "JSON",
		        success: function(data){
		        	hideLoader();
		        	if(data.remained_qty > 0){
		        		if(data.backorders == 1){
		        			 jQuery(that).css("border-color", "#B6B6B6");
				        	 var data = [{"popup_container":".order_one_common_popup", "container_class":"notification", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
						                  "position_top":parseInt(position.top) - 9, "margin_top":"0", "message":data.message, "timeout":"", "display":"true"}];
		        		}else{
		        			 jQuery(that).css("border-color", "#D3441C");
				        	 var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
						                  "position_top":parseInt(position.top) - 9, "margin_top":"0", "message":data.message, "timeout":"", "display":"false"}];
		        		}
		        		showMessage(data);
		        	}else if(data.remained_qty < 0){
		        		 jQuery(that).css("border-color", "#D3441C");
			        	 var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
					                  "position_top":parseInt(position.top) - 9, "margin_top":"0", "message":data.message, "timeout":"", "display":"false"}];
			        	 showMessage(data);
			        }else{
			        	jQuery(that).css("border-color", "#B6B6B6");
			        	if(data.backorders == 1){
				        	 var data = [{"popup_container":".order_one_common_popup", "container_class":"notification", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 45, 
						                  "position_top":parseInt(position.top) - 9, "margin_top":"0", "message":data.message, "timeout":"", "display":"true"}];
				        	 showMessage(data);
		        		}else{
				        	jQuery(".order_one_common_popup").hide();
		        		}
			        }
		        }
		    });
		},300);
	});
			
	//ADD ONE CART
	jQuery(document).on("click", "#one_order_button", function(e){		
		var base_url = jQuery("#base_url").val();
		var position = jQuery(this).position();
		
		var product_id = jQuery("#product_id").val();
		var attributes = {};
		var qty = jQuery("#order_one_qty").val();
				
		var unselected_attribute = "";
		jQuery.each(["first", "second", "third"], function(index, value){
			if(jQuery("#order_one_" + value + "_super_attribute").length > 0){
				var attribute = 0;
				jQuery("[id^=one_" + value + "_]").each(function(index) {
					if(jQuery(this).hasClass("selected")){
						attribute = jQuery(this).attr("id").match(/\d+/)[0];			
					}
				});
				
				if(attribute != 0){
					attributes[jQuery("#order_one_" + value + "_super_attribute").val()] = attribute;
				}else{
					unselected_attribute = value;
					return false;
				}
			}
		});
				
		if(unselected_attribute != ''){
			  var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 110, 
				  		   "position_top":parseInt(position.top) - 6, "margin_top":"0", "message":Translator.translate("Please select a " + unselected_attribute + " attribute."), "timeout":"", "display":"true"}];
			  showMessage(data);
			  return false;
		}else if(!jQuery.isNumeric(qty) || qty <= 0){
			  var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 110, 
		  	    		   "position_top":parseInt(position.top) - 6, "margin_top":"0", "message":Translator.translate("Invalid input for quantity. Please insert positive number."), "timeout":"", "display":"true"}];
			  showMessage(data);
			  return false;
		}
	
		showLoader();
		jQuery.ajax({
	        url: base_url + "quickorder/ajax/addToCart",
	        type: "POST",
	        data: "product_id=" + product_id + "&attributes=" + JSON.stringify(attributes) + "&qty=" + qty,
	        dataType: "JSON",
	        success: function(data){
	        	hideLoader();
	        	if(data.status == "success"){
	        		 var version = data.version.split('.');
	        		 if((data.edition == "Community" && version[1] >= 9) || (data.edition == "Enterprise" && version[1] >= 14)){
	        			 var items = parseInt(jQuery(".skip-link .count").html()) + parseInt(qty);			 
						 jQuery(".skip-link .count").html(items);
						 jQuery(".skip-link .count").show();
						 jQuery("#header-cart").html(data.view);
	        		 }else if(data.edition == "Enterprise" && version[1] < 14){
	        			 jQuery(".top-cart").replaceWith(data.view);
	        		 }else{
	        			 var cart_items = parseInt(jQuery("#cart_items").val()) + parseInt(qty);
		        		 var text = cart_items > 1 ? "My Cart (" + cart_items + " items)" : "My Cart (" + cart_items + " item)";
						 jQuery(".top-link-cart").html(text);
						 jQuery(".top-link-cart").attr("title", text);
						 jQuery("#cart_items").val(cart_items);
	        		 }
	        		
	        		 var data = [{"popup_container":".order_one_common_popup", "container_class":"success", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 110, 
		  	    		   		  "position_top":parseInt(position.top) - 6, "margin_top":"12px", "message":data.message, "timeout":"", "display":"true"}];
	        		 showMessage(data);	        		 	        		
	        		 resetForms();
		        }else{
		        	 var margin_top = "12px";
		        	 if(data.message.length > 51){
		        		 margin_top = "0";
		        	 }
		        	 var data = [{"popup_container":".order_one_common_popup", "container_class":"error", "message_container":".order_one_common_message", "position_left":parseInt(position.left) + 100, 
		  	    		   "position_top":parseInt(position.top) - 2, "margin_top":margin_top, "message":data.message, "timeout":"", "display":"true"}];
	        		 showMessage(data);
		        } 
	        }
	    });
	});
	
	//MULTIPLE DROPDOWN CHANGE
	jQuery(document).on("change", "#order_multiple_dropdown_super_attribute", function(e){
		var base_url = jQuery("#base_url").val();
		var position = jQuery("#multiple_order_button").position();
		var product_id = jQuery("#product_id").val();		
		
		var dropdownValues = {};
		dropdownValues["super_attribute_id"] = jQuery(this).attr("lang");
		dropdownValues["attribute_id"] = jQuery(this).val();
		dropdownValues["price"] = jQuery(this).find(':selected').attr("price");
		
		var verticalValues = jQuery(".order_multiple_vertical_values").length > 0 ? jQuery(".order_multiple_vertical_values").text() : "";
		var horizontalValues = jQuery(".order_multiple_horizontal_values").length > 0 ? jQuery(".order_multiple_horizontal_values").text() : "";		
		
		showLoader();
		jQuery.ajax({
	        url: base_url + "quickorder/ajax/reloadMatrix",
	        type: "POST",
	        data: "product_id=" + product_id + "&dropdownValues=" + JSON.stringify(dropdownValues) + "&verticalValues=" + verticalValues + "&horizontalValues=" + horizontalValues,
	        dataType: "JSON",
	        success: function(data){
	        	hideLoader();	        	
	        	if(data.status == "success"){
	        		jQuery(".order_multiple_dynamic_content").html(data.view);
	        		jQuery(".order_multiple_individual_popup").hide();
		        }else{
		        	 var data = [{"popup_container":".order_multiple_common_popup", "container_class":"error", "message_container":".order_multiple_common_message", "position_left":parseInt(position.left) + 100, 
       		 		  			  "position_top":parseInt(position.top) - 6, "margin_top":"0px", "message": data.message, "timeout":"", "display":"true"}];
		        	 showMessage(data);
		        }
	        }
	    });
	});
		
	//MULTIPLE QUANTITY FOCUS
	jQuery(document).on("focusin", "[id^=multiple_vertical_horizontal_]", function(e){
		if(jQuery(this).val() == 0){
			jQuery(this).val('');
		}
	}).on("focusout", "[id^=multiple_vertical_horizontal_]", function(e){
		if(jQuery(this).val() == ''){
			jQuery(this).val(0);
			jQuery(this).css("border-color", "#B6B6B6");
			jQuery(this).css("cursor", "pointer");
			
	        jQuery("[id^=multiple_vertical_horizontal_]").each(function(index) {
				jQuery(this).removeAttr("disabled");			
			});
	        
	        if(jQuery(".order_multiple_individual_popup").is(":visible")){
	        	jQuery(".order_multiple_individual_popup").hide();
			}       
		}
	});
	
	//CHECK MULTIPLE QUANTITY(KEYUP)
	jQuery(document).on("keyup", "[id^=multiple_vertical_horizontal_]", function(e){
		var ids = jQuery(this).attr("id").split("_");
		var qty = jQuery(this).val();
		var position = jQuery(this).position();
		
		if(!jQuery.isNumeric(qty) || qty < 0){ 
			  jQuery(this).css("border-color", "#D3441C");
			  var data = [{"popup_container":".order_multiple_individual_popup", "container_class":"error", "message_container":".order_multiple_individual_message", "position_left":parseInt(position.left) - 138, 
                       "position_top":parseInt(position.top) + 33, "margin_top":"12px", "message":Translator.translate("Invalid input for quantity. Please insert positive number."), "timeout":"", "display":"false"}];
			  
			  jQuery("[id^=multiple_vertical_horizontal_]").each(function(index) {
				  var temp_ids = jQuery(this).attr("id").split("_");			  
				  if(ids[3] != temp_ids[3] || ids[4] != temp_ids[4]){
					  jQuery(this).attr("disabled", "disabled");			
				  }
			  });
			  
			  showMessage(data);
			  return false;
		}else if(qty > parseInt(jQuery(this).attr("data-quantity"))){
			if(jQuery(this).attr("data-backorders") == 0){
				jQuery(this).css("border-color", "#D3441C");	
			 		  		  
				var data = [{"popup_container":".order_multiple_individual_popup", "container_class":"error", "message_container":".order_multiple_individual_message", "position_left":parseInt(position.left) - 138, 
	                    "position_top":parseInt(position.top) + 33, "margin_top":"12px", "message":jQuery(this).attr("data-message"), "timeout":"", "display":"false"}];
			  
				jQuery("[id^=multiple_vertical_horizontal_]").each(function(index) {
					var temp_ids = jQuery(this).attr("id").split("_");			  
					if(ids[3] != temp_ids[3] || ids[4] != temp_ids[4]){
						jQuery(this).attr("disabled", "disabled");			
					}
				});
				  
				showMessage(data);
				return false;
			}else{
				jQuery(this).css("border-color", "#B6B6B6");
				
				var backorder_quantity = qty - parseInt(jQuery(this).attr("data-quantity"));
				var backorder_message = jQuery(this).attr("data-message");			
				backorder_message = backorder_message.replace("&number", backorder_quantity);
		       	var data = [{"popup_container":".order_multiple_individual_popup", "container_class":"notification", "message_container":".order_multiple_individual_message", "position_left":parseInt(position.left) - 138, 
		                   "position_top":parseInt(position.top) + 33, "margin_top":"12px", "message":backorder_message, "timeout":"5000", "display":"true"}];
		       	showMessage(data);
			}
		}else{
			jQuery(this).css("border-color", "#B6B6B6");
			if(jQuery(".order_multiple_individual_popup").is(":visible")){
	        	jQuery(".order_multiple_individual_popup").hide();
			} 
          
			jQuery("[id^=multiple_vertical_horizontal_]").each(function(index) {
				jQuery(this).removeAttr("disabled");			
			});
		}
	});
	
	//ADD MULTIPLE CART
	jQuery(document).on("click", "#multiple_order_button", function(e){
		var base_url = jQuery("#base_url").val();
		var position = jQuery(this).position();
		
		var product_id = jQuery("#product_id").val();		
		var attributes = [];
		var is_valid_qty = true;
		var counter = 0;
		jQuery("[id^=multiple_vertical_horizontal_]").each(function(index){			
			var ids = jQuery(this).attr("id").split("_");
			var qty = jQuery(this).val();	
			
			if(jQuery.isNumeric(qty) && qty > 0){	
				var data = {};
				if(jQuery("#order_multiple_dropdown_super_attribute").length > 0){
					data[jQuery("#order_multiple_dropdown_super_attribute").attr("lang")] = jQuery("#order_multiple_dropdown_super_attribute").val();
				}
				if(jQuery("#order_multiple_vertical_super_attribute").length > 0){
					data[jQuery("#order_multiple_vertical_super_attribute").val()] = ids[3];
				}
				if(jQuery("#order_multiple_horizontal_super_attribute").length > 0){
					data[jQuery("#order_multiple_horizontal_super_attribute").val()] = ids[4];
				}
				
				attributes.push({
					"data" : data, 
				    "qty" : qty
				});	
			}else{
				if(jQuery.isNumeric(qty) && qty == 0){	
					counter++;
				}else{
					is_valid_qty = false;
					return false;
				}
			}
		});		
		
		if(!is_valid_qty){
			var data = [{"popup_container":".order_multiple_common_popup", "container_class":"error", "message_container":".order_multiple_common_message", "position_left":parseInt(position.left) + 110, 
			     		 "position_top":parseInt(position.top) - 6, "margin_top": "0", "message":Translator.translate("Invalid input for quantity. Please insert positive number."), "timeout":"", "display":"true"}];
		    showMessage(data);
		    return false;
		}else if(jQuery("[id^=multiple_vertical_horizontal_]").length == counter){
			var data = [{"popup_container":".order_multiple_common_popup", "container_class":"error", "message_container":".order_multiple_common_message", "position_left":parseInt(position.left) + 110, 
		 		  		 "position_top":parseInt(position.top) - 6, "margin_top": "0", "message":Translator.translate("Please specify quantites."), "timeout":"", "display":"true"}];
			showMessage(data);
			return false;
		}	
		
		showLoader();		
		jQuery.ajax({
	        url: base_url + "quickorder/ajax/addToCartMultiple",
	        type: "POST",
	        data: "product_id=" + product_id + "&attributes=" + JSON.stringify(attributes),
	        dataType: "JSON",
	        success: function(data){
	        	hideLoader();
		        if(data.status == "success"){		        	
		        	 var version = data.version.split('.');	        		 
		        	 if((data.edition == "Community" && version[1] >= 9) || (data.edition == "Enterprise" && version[1] >= 14)){
		        		 var items = parseInt(jQuery(".skip-link .count").html()) + parseInt(data.added_products);			 
						 jQuery(".skip-link .count").html(items);
						 jQuery(".skip-link .count").show();
						 jQuery("#header-cart").html(data.view);
	        		 }else if(data.edition == "Enterprise" && version[1] < 14){
	        			 jQuery(".top-cart").replaceWith(data.view);
	        		 }else{
	        			 var cart_items = parseInt(jQuery("#cart_items").val()) + parseInt(data.added_products);
		        		 var text = cart_items > 1 ? "My Cart (" + cart_items + " items)" : "My Cart (" + cart_items + " item)";
						 jQuery(".top-link-cart").html(text);
						 jQuery(".top-link-cart").attr("title", text);
						 jQuery("#cart_items").val(cart_items);
	        		 }
					
		        	 var data = [{"popup_container":".order_multiple_common_popup", "container_class":"success", "message_container":".order_multiple_common_message", "position_left":parseInt(position.left) + 110, 
	        		 			  "position_top":parseInt(position.top) - 8, "margin_top":"12px", "message":data.message, "timeout":"", "display":"true"}];
					 showMessage(data);
					 resetForms();
		        }else{
		        	 var margin_top = "0";
		        	 if(data.message.length > 51){
		        		 margin_top = "6px";
		        	 }
		        	 var data = [{"popup_container":".order_multiple_common_popup", "container_class":"error", "message_container":".order_multiple_common_message", "position_left":parseInt(position.left) + 110, 
		        		 		  "position_top":parseInt(position.top) - 8, "margin_top":margin_top, "message": data.message, "timeout":"", "display":"true"}];
		        	 showMessage(data);
		        }
	        }
	    });
	});
	
	// PREV IMG
	jQuery(document).on("click", ".prev img", function(e){
        if(!jQuery("div", jQuery(".order_multiple_scroll_content")).is(":animated")){
        	var prev = parseInt(jQuery(this).attr("data-lang")) - 1;
        	var next = parseInt(jQuery(".next img").attr("data-lang")) - 1;  
        	var width = jQuery("#order_multiple_column_counter_"+prev).outerWidth(true);
        	var last = jQuery("[id^=order_multiple_column_counter_]:last").attr("id").match(/\d+/)[0];

        	jQuery(this).attr("data-lang", prev);
        	jQuery(".next img").attr("data-lang", next);
        	
        	if(prev == 1){
        		jQuery(this).hide();
        	}
        	
        	if(last > next){
        		jQuery(".next img").show();
        	}
        	
        	jQuery(".order_multiple_scroll_content").animate({marginLeft: "+=" + width},300, "swing");        
        }
    });
	
	// NEXT IMG
	jQuery(document).on("click", ".next img", function(e){
        if(!jQuery("div", jQuery(".order_multiple_scroll_content")).is(":animated")){        	
        	var prev = parseInt(jQuery(".prev img").attr("data-lang")) + 1;
        	var next = parseInt(jQuery(this).attr("data-lang")) + 1;     
        	var width = jQuery("#order_multiple_column_counter_" + next).outerWidth(true);
        	var last = jQuery("[id^=order_multiple_column_counter_]:last").attr("id").match(/\d+/)[0];

         	jQuery(".prev img").attr("data-lang", prev);
        	jQuery(this).attr("data-lang", next);
        	
        	if(prev > 1){
        		jQuery(".prev img").show();
        	}
        	
        	if(last == next){
        		jQuery(".next img").hide();
        	}
        	
        	jQuery(".order_multiple_scroll_content").animate({marginLeft: "-=" + width},300, "swing");      	
        }
    });
	
	// SHOW MESSAGE
	function showMessage(data){
		 if(data[0].timeout == ""){
		    var time = 5000;
	     }else{
	        var time = data[0].timeout;
	     }
		 
		jQuery(data[0].popup_container).removeClass("error");
		jQuery(data[0].popup_container).removeClass("success");
		jQuery(data[0].popup_container).removeClass("notification");
		jQuery(data[0].popup_container).addClass(data[0].container_class);
		jQuery(data[0].popup_container).css("left", data[0].position_left);
        jQuery(data[0].popup_container).css("top", data[0].position_top);
        jQuery(data[0].message_container).html(data[0].message);
	    jQuery(data[0].popup_container).show();
	   
	    if(data[0].display == "true"){
		    setTimeout(function(){
		    	jQuery(data[0].popup_container).fadeOut();
		    },time);
	    }
	    return false;
	}
	
	// SHOW LOADER FUNCTION
	function showLoader(){
		var position = jQuery(".configurable_options_container").position();
		var width = jQuery(".configurable_options_container").outerWidth();
		var height = jQuery(".configurable_options_container").outerHeight();
		jQuery(".loader_container").css("left", parseInt(position.left));
		jQuery(".loader_container").css("top", parseInt(position.top));
		jQuery(".loader_container").css("width", width);
		jQuery(".loader_container").css("height", height);
		
		var img_height = parseInt(jQuery(".loader_container img").attr("height"));
		var img_position = parseInt((parseInt(height) - img_height) / 2);
		jQuery(".loader_container img").css("margin-top", img_position);
		jQuery(".loader_container").show();
	}
	
	// HIDE LOADER FUNCTION
	function hideLoader(){
		jQuery(".loader_container").hide();
	}
	
	// RESET FORMS FUNCTION
	function resetForms(){
		jQuery("[id^=one_first_]").removeClass("active");
		jQuery("[id^=one_first_]").removeClass("selected");
		
		jQuery("[id^=one_second_]").removeClass("active");
		jQuery("[id^=one_second_]").removeClass("selected");
		jQuery("[id^=one_second_]").addClass("disabled");
		
		jQuery("[id^=one_third_]").removeClass("active");
		jQuery("[id^=one_third_]").removeClass("selected");
		jQuery("[id^=one_third_]").addClass("disabled");
		
		jQuery("#order_one_qty").val(0);
		jQuery("#order_one_qty").attr("disabled", "disabled");
									
		jQuery("[id^=multiple_vertical_horizontal_]").each(function(index) { 
			 jQuery(this).val(0);		
		});					
	}	
});
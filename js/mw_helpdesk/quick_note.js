var $j_mw = jQuery.noConflict();
/*
$j_mw(document).ready(function(){
	$j_mw('.tooltip').hide();
	
	$j_mw('.special').hover(function(){
	  $j_mw(this).next('.tooltip').show();
	  //$j_mw(this).next('.tooltip').position({at: 'bottom center', of: $(this), my: 'top'})
	});	
	  	
	$j_mw('.special').mouseleave(function(){
		$j_mw('.tooltip').hide();
  	});
});
*/
$j_mw('.special').live('mouseover mouseout', function(event) {
    if (event.type == 'mouseover') { 
		$j_mw(this).next('.tooltip').show();
	}
    else {
		$j_mw('.tooltip').hide();
    }
});
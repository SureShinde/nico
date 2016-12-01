Event.observe('save_order', 'click', function(event) {
	alert('acd');
	var arrInput = document.getElementsByClassName('department_sort_order');

	var params = "";
	for(var i=0;i<arrInput.length;i++){
		params += arrInput[i].title + "-" + arrInput[i].value + "|";
	}
	alert(params);
	window.location = "<?php echo $this->getUrl('*/*/setOrder') ?>items/"+params; 
});
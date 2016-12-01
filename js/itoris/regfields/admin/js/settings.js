if (!ItorisHelper) {
	var ItorisHelper = {};
}

ItorisHelper.toogleFieldEditMode = function(id, container) {
		$(container).disabled = $(id).checked;
};
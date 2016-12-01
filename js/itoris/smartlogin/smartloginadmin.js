if (!SmartLoginHelper) {
	var SmartLoginHelper = {};
}

SmartLoginHelper.toogleFieldEditMode = function(id, container) {
	$(container).disabled = $(id).checked;
	$(container).up().select('input').each(function(elm) {
		elm.disabled = $(id).checked;
	});
};
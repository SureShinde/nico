var OptionsModel = Class.create();
OptionsModel.prototype = {
    initialize : function()
    {
        this.shippingMethodFormId = 'co-shipping-method-form'; 
        this.bindShippingMethods();
    },

    bindShippingMethods : function()
    {
        var shippingMethodSwitchers = $(this.shippingMethodFormId).getInputs('radio', 'shipping_method');
        shippingMethodSwitchers.each(function(radio){
            Event.observe($(radio), 'click', this.checkMethodSelected.bind(this));
            if ($(radio).checked) {
                this.checkMethodSelected({
                    target: $(radio)
                });
            }
        }.bind(this));
    },

    checkMethodSelected : function(event)
    {
        if( !event ) { return; }
        var option = event.target;
        var matches = option.value.match(/(.*)_.*/);

        if (matches && matches[1] && (matches[1] == 'umosaco')) {
            if (tableToShow = $('options-umosaco')) {
                $(tableToShow).select('input', 'select', 'textarea').each(function(elm){
                    $(elm).enable();
                });
                $(tableToShow).show();
            }
        } else {
            if (tableToHide = $('options-umosaco')) {
                $(tableToHide).select('input', 'select', 'textarea').each(function(elm){
                    $(elm).disable();
                });
                $(tableToHide).hide();
            }
        }
    }
};
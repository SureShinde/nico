/**
 * @overridden js file to support option type grid from BundleGrid extension
 */
if(typeof Product=='undefined') {
    var Product = {};
}
/**************************** BUNDLE PRODUCT **************************/
Product.Bundle = Class.create();
Product.Bundle.prototype = {
    initialize: function(config){
        this.config = config;
        // Set preconfigured values for correct price base calculation
        if (config.defaultValues) {
            for (var option in config.defaultValues) {
                if (this.config['options'][option].isMulti) {
                    var selected = new Array();
                    for (var i = 0; i < config.defaultValues[option].length; i++) {
                        selected.push(config.defaultValues[option][i]);
                    }
                    this.config.selected[option] = selected;
                }else if(this.config['options'][option].optionType == 'grid'){
                    var selected = new Array();
                    for (var i = 0; i < config.defaultValues[option].length; i++) {
                        selected.push(config.defaultValues[option][i]);
                    }
                    this.config.selected[option] = selected;
                } else {
                    this.config.selected[option] = new Array(config.defaultValues[option] + "");
                }
            }
        }

        this.reloadPrice();
    },
    changeSelection: function(selection){
        var parts = selection.id.split('-');
        if (this.config['options'][parts[2]].isMulti) {
            selected = new Array();
            if (selection.tagName == 'SELECT') {
                for (var i = 0; i < selection.options.length; i++) {
                    if (selection.options[i].selected && selection.options[i].value != '') {
                        selected.push(selection.options[i].value);
                    }
                }
            } else if (selection.tagName == 'INPUT') {
                selector = parts[0]+'-'+parts[1]+'-'+parts[2];
                selections = $$('.'+selector);

                for (var i = 0; i < selections.length; i++) {
                    if (selections[i].checked && selections[i].value != '') {
                        selected.push(selections[i].value);
                    }
                }
            }
            this.config.selected[parts[2]] = selected;
        } else if(this.config['options'][parts[2]].optionType == 'grid'){
            
            selected = new Array();
            
            if(selection.tagName == 'INPUT'){
                selector = parts[0] + '-' + parts[1] + '-' + parts[2];
                selections = $$('.'+selector);
                
                for (var i = 0; i < selections.length; i++) {
                    if (selections[i].value > 0) {
                        selected.push(selections[i].value);
                    }
                }
            }

            this.config.selected[parts[2]] = selected;

        } else {
            if (selection.value != '') {
                this.config.selected[parts[2]] = new Array(selection.value);
            } else {
                this.config.selected[parts[2]] = new Array();
            }
            this.populateQty(parts[2], selection.value);
            var tierPriceElement = $('bundle-option-' + parts[2] + '-tier-prices'),
                tierPriceHtml = '';
            if (selection.value != '' && this.config.options[parts[2]].selections[selection.value].customQty == 1) {
                tierPriceHtml = this.config.options[parts[2]].selections[selection.value].tierPriceHtml;
            }
            tierPriceElement.update(tierPriceHtml);
        }
        this.reloadPrice();
    },

    reloadPrice: function() {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        var includeTaxPrice = 0;

        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i=0; i < this.config.selected[option].length; i++) {
                    if(this.config.selected[option][i]){
                        var prices = this.selectionPrice(option, this.config.selected[option][i]);
                        this.updateRowPrice(option, this.config.selected[option][i], prices);
                        calculatedPrice += Number(prices[0]);
                        dispositionPrice += Number(prices[1]);
                        includeTaxPrice += Number(prices[2]);
                    }
                    
                }
            }
        }

        //Tax is calculated in a different way for the the TOTAL BASED method
        //We round the taxes at the end. Hence we do the same for consistency
        //This variable is set in the bundle.phtml
        if (taxCalcMethod == CACL_TOTAL_BASE) {
            var calculatedPriceFormatted = calculatedPrice.toFixed(10);
            var includeTaxPriceFormatted = includeTaxPrice.toFixed(10);
            var tax = includeTaxPriceFormatted - calculatedPriceFormatted;
            calculatedPrice = includeTaxPrice - Math.round(tax * 100) / 100;
        }

        //make sure that the prices are all rounded to two digits
        //this is needed when tax calculation is based on total for dynamic
        //price bundle product. For fixed price bundle product, the rounding
        //needs to be done after option price is added to base price
        if (this.config.priceType == '0') {
            calculatedPrice = Math.round(calculatedPrice*100)/100;
            dispositionPrice = Math.round(dispositionPrice*100)/100;
            includeTaxPrice = Math.round(includeTaxPrice*100)/100;

        }

        var event = $(document).fire('bundle:reload-price', {
            price: calculatedPrice,
            priceInclTax: includeTaxPrice,
            dispositionPrice: dispositionPrice,
            bundle: this
        });
        if (!event.noReloadPrice) {
            optionsPrice.specialTaxPrice = 'true';
            optionsPrice.changePrice('bundle', calculatedPrice);
            optionsPrice.changePrice('nontaxable', dispositionPrice);
            optionsPrice.changePrice('priceInclTax', includeTaxPrice);
            optionsPrice.reload();
        }

        return calculatedPrice;
    },

    selectionPrice: function(optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            return 0;
        }
        var qty = null;
        var tierPriceInclTax, tierPriceExclTax;

        if(this.config.options[optionId].optionType == 'grid' && !this.config['options'][optionId].isMulti){
            if ($('bundle-option-' + optionId + '-' + selectionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-' + selectionId + '-qty-input').value;
            } else {
                qty = 0;
            }
        }else if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            if ($('bundle-option-' + optionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-qty-input').value;
            } else {
                qty = 1;
            }
        } else {
            qty = this.config.options[optionId].selections[selectionId].qty;
        }

        if (this.config.priceType == '0') {
            price = this.config.options[optionId].selections[selectionId].price;
            tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

            for (var i=0; i < tierPrice.length; i++) {
                if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
                    price = tierPrice[i].price;
                    tierPriceInclTax = tierPrice[i].priceInclTax;
                    tierPriceExclTax = tierPrice[i].priceExclTax;
                }
            }
        } else {
            selection = this.config.options[optionId].selections[selectionId];
            if (selection.priceType == '0') {
                price = selection.priceValue;
            } else {
                price = (this.config.basePrice*selection.priceValue)/100;
            }
        }
        //price += this.config.options[optionId].selections[selectionId].plusDisposition;
        //price -= this.config.options[optionId].selections[selectionId].minusDisposition;
        //return price*qty;
        var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
            this.config.options[optionId].selections[selectionId].minusDisposition;

        if (this.config.specialPrice) {
            newPrice = (price*this.config.specialPrice)/100;
            price = Math.min(newPrice, price);
        }

        selection = this.config.options[optionId].selections[selectionId];
        if (tierPriceInclTax !== undefined && tierPriceExclTax !== undefined) {
            priceInclTax = tierPriceInclTax;
            price = tierPriceExclTax;
        } else if (selection.priceInclTax !== undefined) {
            priceInclTax = selection.priceInclTax;
            price = selection.priceExclTax !== undefined ? selection.priceExclTax : selection.price;
        } else {
            priceInclTax = price;
        }

        if (this.config.priceType == '1' || taxCalcMethod == CACL_TOTAL_BASE) {
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        }
        else if (taxCalcMethod == CACL_UNIT_BASE) {
            price = (Math.round(price*100)/100).toString();
            disposition = (Math.round(disposition*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*100)/100).toString();
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        } else { //taxCalcMethod == CACL_ROW_BASE)
            price = (Math.round(price*qty*100)/100).toString();
            disposition = (Math.round(disposition*qty*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*qty*100)/100).toString();
            var result = new Array(price, disposition, priceInclTax);
            return result;
        }
    },

    populateQty: function(optionId, selectionId){
        if (selectionId == '' || selectionId == 'none') {
            this.showQtyInput(optionId, '0', false);
            return;
        }
        if (this.config.options[optionId].selections[selectionId].customQty == 1) {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, true);
        } else {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, false);
        }
    },

    showQtyInput: function(optionId, value, canEdit) {
        elem = $('bundle-option-' + optionId + '-qty-input');
        elem.value = value;
        elem.disabled = !canEdit;
        if (canEdit) {
            elem.removeClassName('qty-disabled');
        } else {
            elem.addClassName('qty-disabled');
        }
    },

    changeOptionQty: function (element, event) {
        
        var checkQty = true;
        parts = element.id.split('-');
        
        if (typeof(event) != 'undefined') {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) == 0 || isNaN(Number(element.value)))) {
            if(parts[3] != undefined){
                element.value = '';    
            }else{
                element.value = 1;
            }
            
        }
        optionId = parts[2];
        if(this.config['options'][optionId].optionType == 'grid' && parts[3] != undefined){
            selectionId = parts[3];
            if(this.config.options[optionId].selections[selectionId] != undefined
                && this.config.options[optionId].selections[selectionId].customQty == 1
            ){
                this.config.options[optionId].selections[selectionId].qty = element.value*1;
                this.reloadPrice();
            }
        }else if (!this.config['options'][optionId].isMulti) {
            selectionId = this.config.selected[optionId][0];
            this.config.options[optionId].selections[selectionId].qty = element.value*1;
            this.reloadPrice();
        }
    },

    validationCallback: function (elmId, result){
        if (elmId == undefined || $(elmId) == undefined) {
            return;
        }
        var container = $(elmId).up('ul.options-list');
        if (typeof container != 'undefined') {
            if (result == 'failed') {
                container.removeClassName('validation-passed');
                container.addClassName('validation-failed');
            } else {
                container.removeClassName('validation-failed');
                container.addClassName('validation-passed');
            }
        }
    },

    //this currently does not handle tax algorithms(incl. excl. taxes)
    updateRowPrice: function (optionId, selectionId, prices){
        
        calculatedPrice = prices[0];
        dispositionPrice = prices[1];
        includeTaxPrice = prices[2];

        formattedPrice = optionsPrice.formatPrice(calculatedPrice);
        if($('selection-row-total-' + optionId + '-' + selectionId)){
            $('selection-row-total-' + optionId + '-' + selectionId).innerHTML = '<strong>' + formattedPrice + '</strong>';      
        }
        
    },

    resetSelectionQty: function(selection){
        $(selection.id).value = '';
    },

    validateSelectionStock: function(selectionElem){
        
        var response = {};
        var requestedQty = parseInt(selectionElem.value);
        var selection = selectionElem.id.split('-');

        var optionId = selection[2];
        var selectionId = selection[3];


        var selectionStock = this.config.options[optionId].selections[selectionId].stock;
        if(selectionStock === undefined){
            response['status'] = 'ERROR';
            response['msg'] = 'Invalid selection id or stock is not available for the selection.';

            this.resetSelectionQty(selectionElem);

            return response;
        }

        if(requestedQty < 0){
            response['status'] = 'ERROR';
            response['msg'] = 'Invalid selection qty.';

            this.resetSelectionQty(selectionElem);
            
            return response;
        }

        //requested qty is greater than the configured max qty allowed for item in the cart
        if(requestedQty > parseInt(selectionStock['max_qty'])){
            response['status'] = 'ERROR';
            response['msg'] = 'Maximum ' + parseInt(selectionStock['max_qty']) + ' item(s) can be purchased for current selection.' ;

            this.resetSelectionQty(selectionElem);
            
            return response;
        }

        //requested qty is lower than the min qty allowed for the item
        if((this.isEditMode && parseInt(selectionStock['min_qty']) > 1) 
            && parseInt(selectionStock['min_qty']) > requestedQty){
            response['status'] = 'ERROR';
            response['msg'] = 'Qty can not be lower than ' + parseInt(selectionStock['min_qty']) + ' for the current selection';

            this.resetSelectionQty(selectionElem);

            return response;
        }

        //requsted qty is not available in the stock.
        if(requestedQty > parseInt(selectionStock['qty'])){
            response['status'] = 'ERROR';
            response['msg'] = 'Only ' + parseInt(selectionStock['qty']) + ' item(s) are available in stock for current selection.';

            this.resetSelectionQty(selectionElem);
            
            return response;
        }

        response['status'] = 'SUCCESS';

        return response;

    },

    resetGrid: function(){
        
        var selections = this.config.selected;
        var options = this.config.options;
        
        for (var optionId in selections) {
            if (options[optionId]) {
                for (var i=0; i < selections[optionId].length; i++) {
                    if(selections[optionId][i]){
                        
                        var selectionId = selections[optionId][i];
                        
                        var selection = 'bundle-option-' + optionId + '-' + selectionId;
                        var qtyInput = 'bundle-option-' + optionId + '-' + selectionId + '-qty-input';

                        $(selection).value = '';
                        $(qtyInput).value = '';
                        
                        this.changeOptionQty($(selection));
                        //this.changeSelection($(selection));
                        

                        //var prices = this.selectionPrice(optionId, selectionId);
                        //this.updateRowPrice(optionId, selectionId, prices);
                    }
                    
                }
            }
        }

        this.config.selected = new Array();
        this.config.defaultValues = '';
        this.initialize(this.config);

    }

};

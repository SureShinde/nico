var Checkout = Class.create();
Checkout.prototype = {
    initialize: function (accordion, urls) {
        this.accordion = accordion;
        this.progressUrl = urls.progress;
        this.reviewUrl = urls.review;
        this.saveMethodUrl = urls.saveMethod;
        this.failureUrl = urls.failure;
        this.billingForm = false;
        this.shippingForm = false;
        this.verifyForm = false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';
        this.loadWaiting = false;
        this.steps = ['login', 'billing', 'shipping', 'shipping_method', 'payment', 'verify', 'review'];
        //We use billing as beginning step since progress bar tracks from billing
        this.currentStep = 'billing';
        this.accordion.sections.each(function (section) {
            Event.observe($(section).down('.step-title'), 'click', this._onSectionClick.bindAsEventListener(this));
        }.bind(this));
        this.accordion.disallowAccessToNextSections = true;
    },
    /**
     * Section header click handler
     *
     * @param event
     */
    _onSectionClick: function (event) {
        var section = $(Event.element(event).up().up());

        /**
         * FIX: where clicking on step-title i.e. not on the text or edit link
         * does not reset the relevant progress blocks correctly.
         */
        if (Event.element(event).up().hasClassName('allow')) {
            var section = $(Event.element(event).up());
        }

        if (section.hasClassName('allow')) {
            Event.stop(event);
            this.gotoSection(section.readAttribute('id').replace('opc-', ''), false);
            return false;
        }
    },
    ajaxFailure: function () {
        location.href = this.failureUrl;
    },
    reloadProgressBlock: function (toStep) {
        this.reloadStep(toStep);
        if (this.syncBillingShipping) {
            this.syncBillingShipping = false;
            this.reloadStep('shipping');
        }
    },
    reloadStep: function (prevStep) {
        var updater = new Ajax.Updater(prevStep + '-progress-opcheckout', this.progressUrl, {
            method: 'get',
            onFailure: this.ajaxFailure.bind(this),
            onComplete: function () {
                this.checkout.resetPreviousSteps();
            },
            parameters: prevStep ? {prevStep: prevStep} : null
        });
    },
    reloadReviewBlock: function () {
        var updater = new Ajax.Updater('checkout-review-load', this.reviewUrl, {method: 'get', onFailure: this.ajaxFailure.bind(this)});
    },
    _disableEnableAll: function (element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },
    setLoadWaiting: function (step, keepDisabled) {
        if (step) {
            if (this.loadWaiting) {
                this.setLoadWaiting(false);
            }
            var container = $(step + '-buttons-container');
            container.addClassName('disabled');
            container.setStyle({opacity: .5});
            this._disableEnableAll(container, true);
            Element.show(step + '-please-wait');
        } else {
            if (this.loadWaiting) {
                var container = $(this.loadWaiting + '-buttons-container');
                var isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.removeClassName('disabled');
                    container.setStyle({opacity: 1});
                }
                this._disableEnableAll(container, isDisabled);
                Element.hide(this.loadWaiting + '-please-wait');
            }
        }
        this.loadWaiting = step;
    },
    gotoSection: function (section, reloadProgressBlock) {

        if (reloadProgressBlock) {
            this.reloadProgressBlock(this.currentStep);
        }
        this.currentStep = section;
        var sectionElement = $('opc-' + section);
        sectionElement.addClassName('allow');
        this.accordion.openSection('opc-' + section);
        if (!reloadProgressBlock) {
            this.resetPreviousSteps();
        }
    },
    resetPreviousSteps: function () {
        var stepIndex = this.steps.indexOf(this.currentStep);
        //Clear other steps if already populated through javascript
        for (var i = stepIndex; i < this.steps.length; i++) {
            var nextStep = this.steps[i];
            var progressDiv = nextStep + '-progress-opcheckout';
            if ($(progressDiv)) {
                //Remove the link
                $(progressDiv).select('.changelink').each(function (item) {
                    item.remove();
                });
                $(progressDiv).select('dt').each(function (item) {
                    item.removeClassName('complete');
                });
                //Remove the content
                $(progressDiv).select('dd.complete').each(function (item) {
                    item.remove();
                });
            }
        }
    },
    changeSection: function (section) {
        var changeStep = section.replace('opc-', '');
        this.gotoSection(changeStep, false);
    },
    setMethod: function () {
        if ($('login:guest') && $('login:guest').checked) {
            this.method = 'guest';
            var request = new Ajax.Request(
                    this.saveMethodUrl,
                    {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method: 'guest'}}
            );
            Element.hide('register-customer-password');
            this.gotoSection('billing', true);
        } else if ($('login:register') && ($('login:register').checked || $('login:register').type == 'hidden')) {
            this.method = 'register';
            var request = new Ajax.Request(
                    this.saveMethodUrl,
                    {method: 'post', onFailure: this.ajaxFailure.bind(this), parameters: {method: 'register'}}
            );
            Element.show('register-customer-password');
            this.gotoSection('billing', true);
        } else {
            alert(Translator.translate('Please choose to register or to checkout as a guest').stripTags());
            return false;
        }
        document.body.fire('login:setMethod', {method: this.method});
    },
    setBilling: function () {
        if (($('billing:use_for_shipping_yes')) && ($('billing:use_for_shipping_yes').checked)) {
            shipping.syncWithBilling();
            $('opc-shipping').addClassName('allow');
            this.gotoSection('shipping_method', true);
        } else if (($('billing:use_for_shipping_no')) && ($('billing:use_for_shipping_no').checked)) {
            $('shipping:same_as_billing').checked = false;
            this.gotoSection('shipping', true);
        } else {
            $('shipping:same_as_billing').checked = true;
            this.gotoSection('shipping', true);
        }

        // this refreshes the checkout progress column

//        if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked){
//            shipping.syncWithBilling();
//            //this.setShipping();
//            //shipping.save();
//            $('opc-shipping').addClassName('allow');
//            this.gotoSection('shipping_method');
//        } else {
//            $('shipping:same_as_billing').checked = false;
//            this.gotoSection('shipping');
//        }
//        this.reloadProgressBlock();
//        //this.accordion.openNextSection(true);
    },
    setShipping: function () {
        //this.nextStep();
        this.gotoSection('shipping_method', true);
        //this.accordion.openNextSection(true);
    },
    setShippingMethod: function () {
        //this.nextStep();
        this.gotoSection('payment', true);
        //this.accordion.openNextSection(true);
    },
    setPayment: function () {
        //this.nextStep();
        this.gotoSection('review', true);
        //this.accordion.openNextSection(true);
    },
    setReview: function () {
        this.reloadProgressBlock();
        //this.nextStep();
        //this.accordion.openNextSection(true);
    },
    back: function () {
        if (this.loadWaiting)
            return;
        //Navigate back to the previous available step
        var stepIndex = this.steps.indexOf(this.currentStep);
        var section = this.steps[--stepIndex];
        var sectionElement = $('opc-' + section);
        //Traverse back to find the available section. Ex Virtual product does not have shipping section
        while (sectionElement === null && stepIndex > 0) {
            --stepIndex;
            section = this.steps[stepIndex];
            sectionElement = $('opc-' + section);
        }
        this.changeSection('opc-' + section);
    },
    setStepResponse: function (response) {
        if (response.update_section) {
            $('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
        }
        if (response.allow_sections) {
            response.allow_sections.each(function (e) {
                $('opc-' + e).addClassName('allow');
            });
        }

        if (response.duplicateBillingInfo)
        {
            this.syncBillingShipping = true;
            shipping.setSameAsBilling(true);
        }

        if (response.goto_section) {
            this.gotoSection(response.goto_section, true);
            return true;
        }
        if (response.redirect) {
            location.href = response.redirect;
            return true;
        }
        return false;
    }
};

// billing
var Billing = Class.create();
Billing.prototype = {
    initialize: function (form, addressUrl, saveUrl) {
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function (event) {
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    setAddress: function (addressId) {
        if (addressId) {
            request = new Ajax.Request(
                    this.addressUrl + addressId,
                    {method: 'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout)}
            );
        } else {
            this.fillForm(false);
        }
    },
    newAddress: function (isNew) {
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('billing-new-address-form');
        } else {
            Element.hide('billing-new-address-form');
        }
    },
    resetSelectedAddress: function () {
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value = '';
        }
    },
    fillForm: function (transport) {
        var elementValues = {};
        if (transport && transport.responseText) {
            try {
                elementValues = eval('(' + transport.responseText + ')');
            } catch (e) {
                elementValues = {};
            }
        } else {
            this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^billing:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm) {
                    billingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    setUseForShipping: function (flag) {
        $('shipping:same_as_billing').checked = flag;
    },
    save: function () {
        if (checkout.loadWaiting != false)
            return;
        var validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('billing');
//            if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked) {
//                $('billing:use_for_shipping').value=1;
//            }

            var request = new Ajax.Request(
                    this.saveUrl,
                    {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form)
                    }
            );
        }
    },
    resetLoadWaiting: function (transport) {
        checkout.setLoadWaiting(false);
        document.body.fire('billing-request:completed', {transport: transport});
    },
    /**
     This method recieves the AJAX response on success.
     There are 3 options: error, redirect or html with shipping options.
     */
    nextStep: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
                //Age verification hide show
                if (response.verification_show == 0 || response.verification_show == 2 || response.verification_show == 3) {
                    if (response.verification_show == 3) {
                        alert('We are sorry, but you are not yet of minimum legal age to purchase our products in this state. If you believe you have received this message in error, please contact our customer service team at 888.425.6649 or through our help center ');
                        return false;
                    }

                    jQuery('#opc-verify').hide();
                    jQuery('#verify-progress-opcheckout').hide();
                    getPaymentNumber = parseInt(jQuery('#opc-payment').find('.number').html());
                    jQuery('#opc-review').find('.number').html(getPaymentNumber + 1);
                } else {
                    jQuery('#opc-verify').show();
                    jQuery('#verify-progress-opcheckout').show();
                    getAgeverificationNumber = parseInt(jQuery('#opc-verify').find('.number').html());
                    jQuery('#opc-review').find('.number').html(getAgeverificationNumber + 1);
                }
                //end
            } catch (e) {
                response = {};
            }
        }

        if (response.error) {
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                if (window.billingRegionUpdater) {
                    billingRegionUpdater.update();
                }

                alert(response.message.join("\n"));
            }

            return false;
        }

        checkout.setStepResponse(response);
        payment.initWhatIsCvvListeners();
        // DELETE
        //alert('error: ' + response.error + ' / redirect: ' + response.redirect + ' / shipping_methods_html: ' + response.shipping_methods_html);
        // This moves the accordion panels of one page checkout and updates the checkout progress
        //checkout.setBilling();
    }
};

// shipping
var Shipping = Class.create();
Shipping.prototype = {
    initialize: function (form, addressUrl, saveUrl, methodsUrl) {
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function (event) {
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.methodsUrl = methodsUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    setAddress: function (addressId) {
        if (addressId) {
            request = new Ajax.Request(
                    this.addressUrl + addressId,
                    {method: 'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout)}
            );
        } else {
            this.fillForm(false);
        }
    },
    newAddress: function (isNew) {
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
        }
        shipping.setSameAsBilling(false);
    },
    resetSelectedAddress: function () {
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value = '';
        }
    },
    fillForm: function (transport) {
        var elementValues = {};
        if (transport && transport.responseText) {
            try {
                elementValues = eval('(' + transport.responseText + ')');
            } catch (e) {
                elementValues = {};
            }
        } else {
            this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^shipping:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm) {
                    shippingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    setSameAsBilling: function (flag) {
        $('shipping:same_as_billing').checked = flag;
// #5599. Also it hangs up, if the flag is not false
//        $('billing:use_for_shipping_yes').checked = flag;
        if (flag) {
            this.syncWithBilling();
        }
    },
    syncWithBilling: function () {
        $('billing-address-select') && this.newAddress(!$('billing-address-select').value);
        $('shipping:same_as_billing').checked = true;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField) {
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            //$('shipping:country_id').value = $('billing:country_id').value;
            shippingRegionUpdater.update();
            $('shipping:region_id').value = $('billing:region_id').value;
            $('shipping:region').value = $('billing:region').value;
            //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        } else {
            $('shipping-address-select').value = $('billing-address-select').value;
        }
    },
    setRegionValue: function () {
        $('shipping:region').value = $('billing:region').value;
    },
    save: function () {
        if (checkout.loadWaiting != false)
            return;
        var validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('shipping');
            var request = new Ajax.Request(
                    this.saveUrl,
                    {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form)
                    }
            );
        }
    },
    resetLoadWaiting: function (transport) {
        checkout.setLoadWaiting(false);
    },
    nextStep: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
                //Age verification hide show
                if (response.verification_show == 0 || response.verification_show == 2 || response.verification_show == 3) {
                    if (response.verification_show == 3) {
                        alert('We are sorry, but you are not yet of minimum legal age to purchase our products in this state. If you believe you have received this message in error, please contact our customer service team at 888.425.6649 or through our help center ');
                        return false;
                    }
                    jQuery('#opc-verify').hide();
                    jQuery('#verify-progress-opcheckout').hide();
                    getPaymentNumber = parseInt(jQuery('#opc-payment').find('.number').html());
                    jQuery('#opc-review').find('.number').html(getPaymentNumber + 1);
                } else {
                    jQuery('#opc-verify').show();
                    jQuery('#verify-progress-opcheckout').show();
                    getAgeverificationNumber = parseInt(jQuery('#opc-verify').find('.number').html());
                    jQuery('#opc-review').find('.number').html(getAgeverificationNumber + 1);
                }
                //end
            } catch (e) {
                response = {};
            }
        }
        if (response.error) {
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                if (window.shippingRegionUpdater) {
                    shippingRegionUpdater.update();
                }
                alert(response.message.join("\n"));
            }

            return false;
        }

        checkout.setStepResponse(response);
        /*
         var updater = new Ajax.Updater(
         'checkout-shipping-method-load',
         this.methodsUrl,
         {method:'get', onSuccess: checkout.setShipping.bind(checkout)}
         );
         */
        //checkout.setShipping();
    }
};

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function (form, saveUrl) {
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function (event) {
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        this.saveUrl = saveUrl;
        this.validator = new Validation(this.form);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    validate: function () {
        var methods = document.getElementsByName('shipping_method');
        if (methods.length == 0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
            return false;
        }

        if (!this.validator.validate()) {
            return false;
        }

        for (var i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        alert(Translator.translate('Please specify shipping method.').stripTags());
        return false;
    },
    save: function () {

        if (checkout.loadWaiting != false)
            return;
        if (this.validate()) {
            checkout.setLoadWaiting('shipping-method');
            var request = new Ajax.Request(
                    this.saveUrl,
                    {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form)
                    }
            );
        }
    },
    resetLoadWaiting: function (transport) {
        checkout.setLoadWaiting(false);
    },
    nextStep: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
        }

        if (response.error) {
            alert(response.message);
            return false;
        }

        if (response.update_section) {
            $('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
        }

        payment.initWhatIsCvvListeners();
        if (response.goto_section) {
            checkout.gotoSection(response.goto_section, true);
            checkout.reloadProgressBlock();
            return;
        }

        if (response.payment_methods_html) {
            $('checkout-payment-method-load').update(response.payment_methods_html);
        }

        checkout.setShippingMethod();
    }
};


// payment
var Payment = Class.create();
Payment.prototype = {
    beforeInitFunc: $H({}),
    afterInitFunc: $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    initialize: function (form, saveUrl) {
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    addBeforeInitFunction: function (code, func) {
        this.beforeInitFunc.set(code, func);
    },
    beforeInit: function () {
        (this.beforeInitFunc).each(function (init) {
            (init.value)();
            ;
        });
    },
    init: function () {
        this.beforeInit();
        var elements = Form.getElements(this.form);
        if ($(this.form)) {
            $(this.form).observe('submit', function (event) {
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        var method = null;
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name == 'payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete', 'off');
        }
        if (method)
            this.switchMethod(method);
        this.afterInit();
    },
    addAfterInitFunction: function (code, func) {
        this.afterInitFunc.set(code, func);
    },
    afterInit: function () {
        (this.afterInitFunc).each(function (init) {
            (init.value)();
        });
    },
    switchMethod: function (method) {
        if (this.currentMethod && $('payment_form_' + this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            $('payment_form_' + this.currentMethod).fire('payment-method:switched-off', {method_code: this.currentMethod});
        }
        if ($('payment_form_' + method)) {
            this.changeVisible(method, false);
            $('payment_form_' + method).fire('payment-method:switched', {method_code: method});
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code: method});
        }
        if (method == 'free' && quoteBaseGrandTotal > 0.0001
                && !(($('use_reward_points') && $('use_reward_points').checked) || ($('use_customer_balance') && $('use_customer_balance').checked))
                ) {
            if ($('p_method_' + method)) {
                $('p_method_' + method).checked = false;
                if ($('dt_method_' + method)) {
                    $('dt_method_' + method).hide();
                }
                if ($('dd_method_' + method)) {
                    $('dd_method_' + method).hide();
                }
            }
            method == '';
        }
        if (method) {
            this.lastUsedMethod = method;
        }
        this.currentMethod = method;
    },
    changeVisible: function (method, mode) {
        var block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].each(function (el) {
            element = $(el);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                element.select('input', 'select', 'textarea', 'button').each(function (field) {
                    field.disabled = mode;
                });
            }
        });
    },
    addBeforeValidateFunction: function (code, func) {
        this.beforeValidateFunc.set(code, func);
    },
    beforeValidate: function () {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function (validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    validate: function () {
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length == 0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.').stripTags());
            return false;
        }
        for (var i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        alert(Translator.translate('Please specify payment method.').stripTags());
        return false;
    },
    addAfterValidateFunction: function (code, func) {
        this.afterValidateFunc.set(code, func);
    },
    afterValidate: function () {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function (validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    save: function () {
        if (checkout.loadWaiting != false)
            return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            checkout.setLoadWaiting('payment');
            var request = new Ajax.Request(
                    this.saveUrl,
                    {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form)
                    }
            );
        }
    },
    resetLoadWaiting: function () {
        checkout.setLoadWaiting(false);
    },
    nextStep: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
        }
        /*
         * if there is an error in payment, need to show error message
         */
        if (response.error) {
            if (response.fields) {
                var fields = response.fields.split(',');
                for (var i = 0; i < fields.length; i++) {
                    var field = null;
                    if (field = $(fields[i])) {
                        Validation.ajaxError(field, response.error);
                    }
                }
                return;
            }
            if (typeof (response.message) == 'string') {
                alert(response.message);
            } else {
                alert(response.error);
            }
            return;
        }

        checkout.setStepResponse(response);
        //checkout.setPayment();
    },
    initWhatIsCvvListeners: function () {
        $$('.cvv-what-is-this').each(function (element) {
            Event.observe(element, 'click', toggleToolTip);
        });
    }
};

var Review = Class.create();
Review.prototype = {
    initialize: function (saveUrl, successUrl, agreementsForm) {
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.agreementsForm = agreementsForm;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    save: function () {
        if (checkout.loadWaiting != false)
            return;
        checkout.setLoadWaiting('review');
        var params = Form.serialize(payment.form);
        if (this.agreementsForm) {
            params += '&' + Form.serialize(this.agreementsForm);
        }
        params.save = true;
        var request = new Ajax.Request(
                this.saveUrl,
                {
                    method: 'post',
                    parameters: params,
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout)
                }
        );
    },
    resetLoadWaiting: function (transport) {
        checkout.setLoadWaiting(false, this.isSuccess);
    },
    nextStep: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
            if (response.redirect) {
                this.isSuccess = true;
                location.href = response.redirect;
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                window.location = this.successUrl;
            } else {
                var msg = response.error_messages;
                if (typeof (msg) == 'object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg);
                }
            }

            if (response.update_section) {
                $('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section, true);
            }
        }
    },
    isSuccess: false
};

//Js for verify step
var VerifyMethod = Class.create();
VerifyMethod.prototype = {
    initialize: function (form, saveUrl, uploadUrl) {
        this.form = form;

        if ($(this.form)) {
            $(this.form).observe('submit', function (event) {
                this.save();
                Event.stop(event);
            }.bind(this));
        }

        this.saveUrl = saveUrl;
        this.uploadUrl = uploadUrl;
        this.validator = new Validation(this.form);
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    validate: function () {

        verifyStep = jQuery('#verify-step').val();
        if (verifyStep == 1) {
            jQuery('#verify-step1-error').hide();
            if (jQuery('#verify-month').val() == '') {
                jQuery('#verify-step1-error').html('Please select a valid Date Of Birth').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step1-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-day').val() == '') {
                jQuery('#verify-step1-error').html('Please select a valid Date Of Birth').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step1-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-year').val() == '') {
                jQuery('#verify-step1-error').html('Please select a valid Date Of Birth').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step1-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-step1').css('display') != 'none') {
                isConfirm = confirm("A correct birthdate is essential for proper age verification. If your selected date of birth is correct, click OK. To edit, click Cancel.");
                if (isConfirm == true) {
                    return true;
                } else {
                    return false;
                }
            }
        } else if (verifyStep == 2) {
            jQuery('#verify-step2-error').hide();
            if (jQuery('#verify-social').val() == '') {
                jQuery('#verify-step2-error').html('Enter SSN Number.').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step2-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-social').val().length < 4) {
                jQuery('#verify-step2-error').html('Enter 4 digits of SSN Number.').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step2-error').fadeOut();
                }, 5000);
                return false;
            }

            if (jQuery('#verify-month2').val() == '') {
                jQuery('#verify-step2-error').html('Please select a valid Date Of Birth.').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step2-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-day2').val() == '') {
                jQuery('#verify-step2-error').html('Please select a valid Date Of Birth.').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step2-error').fadeOut();
                }, 5000);
                return false;
            }
            if (jQuery('#verify-year2').val() == '') {
                jQuery('#verify-step2-error').html('Please select a valid Date Of Birth.').css('color', 'red').show();
                setTimeout(function () {
                    jQuery('#verify-step2-error').fadeOut();
                }, 5000);
                return false;
            }

            if (jQuery('#verify-step2').css('display') != 'none') {
                isConfirm = confirm("A correct SSN entry and birthdate is essential for proper age verification. If the last 4 of your SSN and date of birth are correct, click OK. To edit, click Cancel.");
                if (isConfirm == true) {
                    return true;
                } else {
                    return false;
                }
            }

        } else if (verifyStep == 3) {

            jQuery('#verify-step3-error').hide();

            var file = jQuery('#verify-doc');


            if (file.val() == '') {

                jQuery('#verify-step3-error').html('Please upload a photo ID.')
                        .css('color', 'red')
                        .show();

                setTimeout(function () {
                    jQuery('#verify-step3-error').fadeOut();
                }, 5000);

                return false;
            }

            var isValid = validateFileExtension(jQuery('#verify-doc'));
            if (!isValid) {
                return false;
            }
        }
        return true;
    }
    ,
    save: function () {

        if (checkout.loadWaiting != false) {
            return;
        }

        if (this.validate()) {

            verifyStep = jQuery('#verify-step');
            verifyStatus = jQuery('#verify-status');
            if ((verifyStep.val() == 1 || verifyStep.val() == 2 || verifyStep.val() == 3) && verifyStatus.val() == 1) {
                disableContinueButton();
            }
            if (verifyStep.val() == 3) {
                jQuery('#verify-doc').prop('disabled', true);       //disable attach button
                jQuery('.age-verify-submit').prop('disabled', true);       //disable attach button
            }
            disableSubmitButton();

            checkout.setLoadWaiting('verify');
            var request = new Ajax.Request(
                    this.saveUrl,
                    {
                        method: 'post',
                        onComplete: this.onComplete,
                        onSuccess: this.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(this.form),
                    }
            );

        }


    },
    resetLoadWaiting: function (transport) {
        checkout.setLoadWaiting(false);
    },
    nextStep: function (transport) {

        enableSubmitButton();
        enableContinueButton();

        if (transport && transport.responseText) {


            try {
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }

            if (response.error) {
                alert(response.message);
                return false;
            }


            verifyStep = jQuery('#verify-step');
            verifyStatus = jQuery('#verify-status');

            respTxt = transport.responseText.trim().split('---');
            if ((respTxt[0] == '\"ERROR') && (verifyStep.val() == '1')) {

                jQuery('#verify-step1').hide();
                jQuery('#verify-step2').show();
                jQuery('#verify-error-message').html('We are sorry, we were unable to verify your Date Of Birth with the information provided. To proceed with your order, please confirm your age using one of the two methods listed below.').css('display', 'block');
                jQuery('#verify-submit-button').hide();
                verifyStep.val('2');
                //setting dropdown on second step
                jQuery('#verify-month2').val(jQuery('#verify-month').val());
                jQuery('#verify-day2').val(jQuery('#verify-day').val());
                jQuery('#verify-year2').val(jQuery('#verify-year').val());
                jQuery('#verify-submit-button').removeClass('submit-button-step3');
                jQuery('#verify-submit-button').removeClass('submit-top-58');
                return;
            } else if ((respTxt[0] == '\"ERROR') && (verifyStep.val() == '2')) {
                jQuery('#verify-error-message').html('We are sorry, we were unable to verify your Date Of Birth with the information provided. To proceed with your order, please upload a photo or scan of a government issued ID').css('display', 'block');
                jQuery('#verify-step2').hide();
                jQuery('#verify-method2-content').show();
                jQuery('.upload-attached').show();
                verifyStep.val('3');
                jQuery('.step3-back-button').hide();
                jQuery('#verify-submit-button').addClass('submit-button-step3');
                jQuery('#verify-submit-button').addClass('submit-top-58');
                return;

            } else if (verifyStep.val() == '3' && (verifyStatus.val() == 0)) {

                var formElement = jQuery("#co-verify-form");
                var formData = new FormData();
                formData.append('filename', jQuery('input[type=file]')[0].files[0]);
                jQuery.ajax({
                    type: 'POST',
                    url: this.uploadUrl,
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        response = response.trim();
                        if (response != 'null') {
                            var data = JSON.parse(response);
                            if (data.error != undefined) {
                                alert(data.message);
                                return false;
                            }
                        }
                        jQuery('#verify-submit-button').hide();
                        jQuery('#verify-step1-thanks').hide();
                        jQuery('#verify-method2-content').hide();
                        jQuery('#verify-error-message').hide();
                        jQuery('#co-verify-form').find('#verify-buttons-container').css({"display": "block"});
                        jQuery('#co-verify-form').find('#verify-button-continue').css({"display": "block"});
                        jQuery('#co-verify-form').find('#verify-please-wait').css({"display": "none"});
                        jQuery('#verify-step3-thanks').show();

                        jQuery('.upload-attached').hide();
                        jQuery('#verify-status').val('1');

                    }
                });
                return;
            } else if (verifyStep.val() == '1' && (respTxt[0] != '\"ERROR') && (verifyStatus.val() == '0')) {
                jQuery('#verify-submit-button').hide();
                jQuery('#verify-step1-thanks').show();
                jQuery('#co-verify-form').find('#verify-button-continue').css({"display": "block"});
                jQuery('#verify-step1').hide();
                jQuery('#verify-status').val('1');
                jQuery('#co-verify-form').find('#verify-buttons-container').css({"display": "block"});
                return;
            } else if (verifyStep.val() == '2' && (respTxt[0] != '\"ERROR') && (verifyStatus.val() == '0')) {
                jQuery('#verify-submit-button').hide();
                jQuery('#verify-step1-thanks').show();
                jQuery('#co-verify-form').find('#verify-button-continue').css({"display": "block"});
                jQuery('#verify-step2').hide();
                jQuery('#verify-status').val('1');
                jQuery('#co-verify-form').find('#verify-buttons-container').css({"display": "block"});
                return;
            }



            if (response.update_section) {
                $('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
            }


            if (response.goto_section) {
                checkout.gotoSection(response.goto_section);
                checkout.reloadProgressBlock('verify');
                return;
            }

            checkout.setReview();

        }
    }
};

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function isValidDOB(testDate) {

    var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/;

    if (!date_regex.test(testDate)) {
        alert('Either DOB is not in format MM/DD/YYYY or incorrect!');
        return false;
    }

    isConfirm = confirm("A correct birthdate is essential for proper age verification. If your selected date of birth is correct, click OK. To edit, click Cancel.");
    if (isConfirm) {
        return true;
    }

    return false;
}

function showVerifySSNHtml() {
    jQuery('#verify-step').val('2');
    jQuery('#verify-step2-buttons').hide();
    jQuery('#verify-method1-content').show();
    jQuery('#verify-error-message').hide();
    jQuery('#verify-submit-button').show();
    jQuery('#verify-submit-button').removeClass('submit-button-step3');
    jQuery('#verify-submit-button').removeClass('submit-top-58');
}

function showVerifyPhotoIdHtml() {
    jQuery('#verify-step2-buttons').hide();
    jQuery('#verify-method2-content').show();
    jQuery('.upload-attached').show();
    jQuery('#verify-error-message').hide();
    jQuery('#verify-submit-button').show();
    jQuery('#verify-step').val('3');
    jQuery('#verify-submit-button').addClass('submit-button-step3');

}

function setFileName() {
    jQuery('.upload-attached').show().html(jQuery('#verify-doc').val());
    jQuery('#verify-method2-file-name').removeClass('show-file-info');
}

function getBackStep() {
    verifyStep = jQuery('#verify-step').val();
    verifyStatus = jQuery('#verify-status').val();
    //alert(verifyStep + '----' + verifyStatus);
    if (verifyStep == 2) {
        jQuery('#verify-method1-content').hide();
        jQuery('#verify-submit-button').hide();
        jQuery('#verify-step2-buttons').show();
    } else if (verifyStep == 3) {
        jQuery('#verify-method2-content').hide();
        jQuery('#verify-submit-button').hide();
        jQuery('#verify-step2-buttons').show();
    }
}

function disableSubmitButton() {
    jQuery('#verify-submit-button').addClass('disabled');
    jQuery('#verify-please-wait').show();
    jQuery('.step2-back-button').addClass('disable-anchor');
    jQuery('.step3-back-button').addClass('disable-anchor');
}

function enableSubmitButton() {
    jQuery('#verify-submit-button').removeClass('disabled');
    jQuery('#verify-please-wait').hide();
    jQuery('.step2-back-button').removeClass('disable-anchor');
    jQuery('.step3-back-button').removeClass('disable-anchor');
}

function disableContinueButton() {
    jQuery('#verify-button-continue').hide();
    jQuery('#verify-please-wait2').show();
}

function enableContinueButton() {
    jQuery('#verify-button-continue').show();
    jQuery('#verify-please-wait2').hide();
}
function validateFileExtension(fileElem) {

    var allowedFiles = [".jpg", ".jpeg", ".png"];
    var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");

    if (!regex.test(fileElem.val().toLowerCase())) {

        jQuery('#verify-step3-error').html('Please upload image with .jpg, .jpeg and .png extensions only!')
                .css('color', 'red')
                .show();

        setTimeout(function () {
            jQuery('#verify-step3-error').fadeOut();
        }, 5000);

        return false;
    }

    return true;
}
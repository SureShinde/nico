MageParts.SmoothForm = Class.create({

    initialize: function(config) {
        var submitBtn, key, that = this;

        this.formEl = null;
        this.formElId = '';
        this.actionUrl = '';
        this.captchaEl = null;
        this.captchaFormId = '';
        this.includeCaptcha = '';
        //this.messageContainerEl = null; <-- Gammal property, utbytt mot {this.messageBox}.
        //this.ajax = null; <-- Gammal property.
        this.varienForm = null;
        this.messageBox = null;
        this.ajaxLoader = new AjaxLoader({
            template: $('ajax-loader')
        });

        for (key in config) {
            if (this.hasOwnProperty(key)) {
                this[key] = config[key];
            }
        }

        if (this.formEl) {
            if (this.formEl.id) {
                this.varienForm = new VarienForm(this.formEl.id);
            }

            this.formEl.getElements().each(function(input) {
                if (input.type === 'submit') {
                    that.ajaxLoader.pointer = input;
                }
                else if (input.type !== 'submit' || input.type !== 'button') {
                    input.observe('focus', function(e) {
                        that.showCaptcha();
                    });
                }
            });

            this.formEl.observe('submit', function(e) {
                if (that.varienForm && that.varienForm.validator && that.varienForm.validator.validate()) {
                    that.ajaxLoader.show();

                    AJAX.queue({
                        url: that.actionUrl,
                        options: {
                            method: 'post',
                            parameters: that.formEl.serialize(true),

                            onComplete: function(transport) {
                                var data = transport.responseJSON;

                                var hideCaptcha = true;

                                if (data && data.msg) {
                                    if (data.msg.success && data.msg.success.length > 0) {
                                        data.msg.success.each(function(msg) {
                                            that.messageBox.addSuccess(msg);
                                        });
                                    }

                                    if (data.msg.error && data.msg.error.length > 0) {
                                        hideCaptcha = false;

                                        data.msg.error.each(function(msg) {
                                            that.messageBox.addError(msg);
                                        });
                                    }

                                    if (data.msg.notice && data.msg.notice.length > 0) {
                                        var hideCaptcha = false;

                                        data.msg.notice.each(function(msg) {
                                            that.messageBox.addNotice(msg);
                                        });
                                    }
                                }

                                if (hideCaptcha) {
                                    that.hideCaptcha(transport);
                                } else {
                                    that.refreshCaptcha();
                                }

                                that.ajaxLoader.hide();
                            }
                        }
                    }).
                        run();
                }
            });
        }
    },

    showCaptcha: function() {
        if (this.captchaEl && this.captchaEl.style.display === 'none') {
            jQuery(this.captchaEl).add(this.getCaptchaReloadElement()).stop().fadeIn();
        }
    },

    hideCaptcha: function(transport) {
        if (transport !== undefined) {
            if (transport.msg && transport.msg.error && transport.msg.error.length > 0) {
                // automatically refresh captcha, it becomes invalid if you first post an incorrect response then
                this.refreshCaptcha();
                return;
            }
        }

        if (this.captchaEl && this.captchaEl.style.display !== 'none') {
            jQuery(this.captchaEl).add(this.getCaptchaReloadElement()).stop().fadeOut({complete: this.refreshCaptcha()});
        }
    },

    getCaptchaReloadElement: function() {
        return $(this.captchaEl.id + '-reload');
    },

    getCaptchaInput: function() {
        var result = null;

        if (this.captchaFormId !== '') {
            result = $('captcha_' + this.captchaFormId);
        }

        return result;
    },

    resetCaptchaInput: function() {
        if (this.getCaptchaInput()) {
            this.getCaptchaInput().setValue('');
        }
    },

    refreshCaptcha: function() {
        if (this.captchaFormId !== '') {
            var captchaReloadImgBox = $$('#captcha-image-box-' + this.captchaFormId + ' .captcha-reload');

            if (captchaReloadImgBox && captchaReloadImgBox.length) {
                captchaReloadImgBox = captchaReloadImgBox[0];
            }

            $(this.captchaFormId).captcha.refresh(captchaReloadImgBox);
        }

        if (this.getCaptchaInput()) {
            this.resetCaptchaInput();
        }
    }

});

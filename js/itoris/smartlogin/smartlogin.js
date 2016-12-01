var smartLogin = {
	showStep:	0,
	delay:	30,
	step:	0.1,
	callUrlLogin:	'',
	callUrlCreate:	'',
	callUrlRetrieve:	'',
	openWindow:	'',
	curentInput:	'',
	colors:{
		SUCCESS_MSG_COLOR: '#419f2c',
		DEFAULT_MSG_ERROR_COLOR: '#f25016'
	},
	sizes:{
		SIZE_AND_OPACITY_STEP: 10,
		SHOW_WINDOW_TOP: 60,
		SHOW_WINDOW_LEFT: 60,
		HIDE_WINDOW_TOP:50,
		HIDE_WINDOW_LEFT:50,
		WINDOW_WIDTH: 315,
		LOGIN_WINDOW_HEIGHT: 332,
		REGISTRATION_WINDOW_HEIGHT: 480,
		RETRIEVE_WINDOW_HEIGHT: 205
	},
	translates : {
		EMAIL_AND_PASSWORD_REQUIRED : 'Both the Email and Password are required',
		EMAIL_INVALID : 'Please enter a valid email address',
		SHORT_PASSWORD : 'Password should be at least 6 characters',
		ALL_FIELDS_ARE_REQUIRED : 'All fields marked with * are required',
		PASSWORDS_NOT_MATCH : 'Please make sure your passwords match',
		EMAIL_IS_REQUIRED : 'Email field is required',
		PASSWORD_SEND: 'A new password has been sent'
	},
	validators : {
		requiredFieldIsFilled: function(params){
			return params.field.value=='' ? false : true;
		},
		isEmailValid: function(params){
			return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@(([a-z0-9][a-z0-9]*|[a-z0-9][a-z0-9\-]*[a-z0-9])\.)+[a-z]{2,4}$/i).test(params.field.value);
		},
		isPasswordLengthValid: function(params){
			return params.field.value.length >= 6;
		},
		passwordsAreMatch:function(params){
			return params.field.value== params['fieldConfirm'].value;
		}
	},

	loadingMask: function(window) {
		var mask = $$('#smartlogin .loading-mask')[0];
		mask.show();
		var width = this.openWindow.getWidth();
		var height = this.openWindow.getHeight();
		if (this.openWindow.hasClassName('dialog')) {
			 width -= 8;
		} else {
			width -= 32;
			height -= 34;
		}
		mask.setStyle({
			width: width + 'px',
			height: height + 'px',
			marginLeft: -parseInt(width / 2) + 'px',
			marginTop: -parseInt(height / 2) + 'px'
		});

		$$('#smartlogin .loading-mask')[0].className = 'loading-mask loading-mask-'+window+'';
	},

	elementWidth: function(element){
		return element.style.width.substr(0,element.style.width.indexOf('p'));
	},

	elementHeight: function(element){
		return element.style.height.substr(0,element.style.height.indexOf('p'));
	},

	hideMasks: function(){
		Element.hide($$('#smartlogin .loading-mask')[0]);
		Element.hide($$('#smartlogin .overlay-modal')[0]);
	},

	hideSmartDialogs: function(){
		smartLogin.showStep =0;
		smartLogin.hideSmart(smartLogin.openWindow,'',1);
	},

	hideSmart : function(element,windowToOpen,hideStep, redirectUrl){
		element.style.opacity = hideStep;
		element.style.width = hideStep*smartLogin.elementWidth(element)+'px';
		element.style.height= hideStep*smartLogin.elementHeight(element)+'px';
		element.style.left = smartLogin.sizes.HIDE_WINDOW_LEFT+'%';
		element.style.top = smartLogin.sizes.HIDE_WINDOW_TOP+'%';
		hideStep= hideStep - smartLogin.step;
		if (hideStep > 0){
			setTimeout(function(element,windowToOpen,hideStep){
							return function(){
											smartLogin.hideSmart(element,windowToOpen,hideStep, redirectUrl);
							}
						}(element,windowToOpen,hideStep), this.delay);
		}else{
			element.style.width = '0px';
			element.style.height= '0px';
			switch (windowToOpen)	{
					case 'login':
							this.showSmartLoginDialog();
							break;

					case 'register':
							this.showSmartRegisterDialog();
							break;

					case 'retrieve':
							this.showSmartRetrieveDialog();
							break;

					case 'reload':
							if (redirectUrl) {
								location.href = redirectUrl;
							} else {
								location.reload();
							}
							break;

					default:
							if (windowToOpen!=''){
								location.replace(windowToOpen);
							}else{
								break;
							}
			}
		}
		if (element.hasClassName('dialog')) {
			element.setStyle({overflow: 'hidden'});
		}
		Element.hide($$('#smartlogin .overlay-modal')[0]);
	},

	showSmartDialog: function(element,windowHeight){
		if (!element) return;
		smartLogin.openWindow = element;
		Element.show($$('#smartlogin .overlay-modal')[0]);
		element.style.opacity = smartLogin.showStep;
		/**
		 * code for changing opacity in IE7
		 */
		//element.style.filter = 'alpha(opacity='+ this.showStep*100 + ')';
		element.style.width = this.showStep*smartLogin.sizes.WINDOW_WIDTH+'px';
		element.style.height= this.showStep*windowHeight+'px';
		element.style.left = smartLogin.sizes.SHOW_WINDOW_LEFT-this.showStep*smartLogin.sizes.SIZE_AND_OPACITY_STEP+'%';
		element.style.top = smartLogin.sizes.SHOW_WINDOW_TOP-this.showStep*smartLogin.sizes.SIZE_AND_OPACITY_STEP+'%';
		smartLogin.showStep += smartLogin.step;
		if (smartLogin.showStep < 1) {
			setTimeout(function(element,windowHeight){
							return function(){
											smartLogin.showSmartDialog(element,windowHeight);
							}
						}(element,windowHeight), this.delay);
		} else {
			if (element.hasClassName('dialog')) {
				smartLogin.calculateElementOffset(element);
			}
			smartLogin.openWindow.select('input')[0].focus();
		}
/**
 * code for better loading of shadows in IE7
 */
//		else{
//			element.style.filter = 'none';
//			element.style.backgroundColor = 'transparent';
//		}
	},

	calculateElementOffset: function(element) {
		var width =  570;
		if (element.hasClassName('custom-reg-form')) {
			if (element.select('.itoris-smartlogin-form-2cols').length) {
				width = 667;
			} else if (element.select('.itoris-smartlogin-form-col').length) {
				width = 334
			} else {
				width = 980;
			}
		}
		element.setStyle({overflow: 'visible', width: width + 'px', height: 'auto'});

		if (element.hasClassName('dialog-reg')) {
			var marginTop = 0;
			var elementTop = document.viewport.getScrollOffsets()[1];
			elementTop += (element.getHeight() > document.viewport.getHeight()) ? 10 : ((document.viewport.getHeight() - element.getHeight()) / 2);
			element.setStyle({top: elementTop + 'px'});
		} else {
			var marginTop = (element.getHeight() > document.viewport.getHeight()) ? ((document.viewport.getHeight() / 2) - 10) : (element.getHeight() / 2);
		}
		element.setStyle({height: element.getHeight() + 'px', marginLeft: -(element.getWidth()/2) + 'px', marginTop: -(marginTop) + 'px'});
		var title = element.select('.title')[0];
		// titleText because ie7 calculates the wrong width of the title
		var titleText =  title.select('.right span')[0];
		var titleMargin = parseInt((element.getWidth() - titleText.getWidth()) / 2);
		title.setStyle({left: titleMargin + 'px', right: (titleMargin - 5) + 'px'});
	},

	clearFields: function(){
		if (!smartLogin.openWindow) return true;
		smartLogin.openWindow.select('input').each(function(element){
			element.value = '';
		});
		$$('#smartlogin .field-error').each(function(elem){
				elem.removeClassName('field-error');
		});
		if ($$('#smartlogin .issubscribed')[0]) {
			$$('#smartlogin .issubscribed')[0].checked = false;
		}
		return false;
	},

	showSmartLoginDialog: function(){
		smartLogin.showSmartDialog($$('#smartlogin .dialog-log')[0],smartLogin.sizes.LOGIN_WINDOW_HEIGHT);
		return smartLogin.clearFields();
	},

	showSmartRegisterDialog: function(){
		if (!$$('#smartlogin .dialog-reg.smartform')[0]) return;
		smartLogin.showSmartDialog($$('#smartlogin .dialog-reg')[0],smartLogin.sizes.REGISTRATION_WINDOW_HEIGHT);
		if (smartLogin.clearFields()) return true;
		smartLogin.reloadCaptcha();
		smartLogin.addValueToCheckbox();
		return false;
	},

	showSmartRetrieveDialog:function(){
		smartLogin.showSmartDialog($$('#smartlogin .dialog-ret')[0],smartLogin.sizes.RETRIEVE_WINDOW_HEIGHT);
		return smartLogin.clearFields();
	},

	addValueToCheckbox: function() {
		var inputs = smartLogin.openWindow.select('input');
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].type == 'checkbox') {
				inputs[i].value = 1;
				inputs[i].setStyle({width: 'auto'});
			}
		}
	},

	displayError: function(message){
		var mask = smartLogin.openWindow.className.substr(smartLogin.openWindow.className.indexOf('-')+1,3);
		smartLogin.loadingMask(mask);
		$$('#smartlogin .error .error-text')[0].update(message);
		smartLogin.showErrorWindow();
		smartLogin.openWindow.focus();
		var captchaElements = smartLogin.openWindow.select('.reload-captcha');
		if (captchaElements.length) {
			for (var i = 0; i < captchaElements.length; i++) {
				captchaElements[i].fire('click');
			}
		}
		smartLogin.reloadCaptcha();
	},

	showErrorWindow : function() {
		var errorWindow = $$('#smartlogin .error')[0];
		errorWindow.style.display='block';
		var errorWindowWidth = errorWindow.getWidth();
		var marginLeft = 0;
		var width = 0;
		if (Prototype.Browser.IE) {
			var versionIE = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5));
			if (versionIE == 7) {
				var message = $$('#smartlogin .error .error-text')[0].innerText;
				errorWindowWidth = parseInt(message.length * 11);
			}
			if (versionIE == 9) {
				errorWindowWidth += 10;
			}
		}
		marginLeft = parseInt(errorWindowWidth/2, 10);
		width = errorWindowWidth;
		errorWindow.setStyle({'width' : width + 'px', 'marginLeft' : '-' + marginLeft + 'px'});
	},

	validate:function(list){
		$$('#smartlogin .error .error-text')[0].style.color = smartLogin.colors.DEFAULT_MSG_ERROR_COLOR;
		$$('#smartlogin .error')[0].style.display='none';
		$$('#smartlogin .field-error').each(function(elem){
				elem.removeClassName('field-error');
		});
		for (var i = 0; i <list.length; i++) {
			if(!smartLogin.validators[list[i]['validator']](list[i])){
				smartLogin.displayError(list[i]['errorMsg']);
				$(list[i]['field'].parentNode).addClassName('field-error');
				smartLogin.curentInput = list[i]['field'];
				return false;
			}
		}
		return true;
	},

	validateLoginFilds: function (){
		var list = [
			{
				'field' 	: $$('#smartlogin .login-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.EMAIL_AND_PASSWORD_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .login-input')[1],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.EMAIL_AND_PASSWORD_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .email.login-input')[0],
				'validator' : 'isEmailValid',
				'errorMsg' 	: smartLogin.translates.EMAIL_INVALID
			},
			{
				'field' 	: $$('#smartlogin .password.login-input')[0],
				'validator' : 'isPasswordLengthValid',
				'errorMsg' 	: smartLogin.translates.SHORT_PASSWORD
			}
		];
		return smartLogin.validate(list);
	},

	ajaxLogin: function(){
		if (smartLogin.validateLoginFilds()){
			smartLogin.loadingMask('log');
			var url = smartLogin.callUrlLogin;
			url += (url.indexOf('?') == -1) ? '?' : '&';
			url += 'login[email]=' + $$('#smartlogin .email.login-input')[0].value;
			url += '&login[password]=' + $$('#smartlogin .password.login-input')[0].value;
			JSONP(url, function () {});
		}
	},

	validateRegisterFields: function(){
		if (smartLogin.openWindow.hasClassName('custom-reg-form')) {
			return true;
		}
		var list = [
			{
				'field' 	: $$('#smartlogin .fistname.reg-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.ALL_FIELDS_ARE_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .lastname.reg-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.ALL_FIELDS_ARE_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .email.reg-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.ALL_FIELDS_ARE_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .password.reg-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.ALL_FIELDS_ARE_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .confirmpassword.reg-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.ALL_FIELDS_ARE_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .email.reg-input')[0],
				'validator' : 'isEmailValid',
				'errorMsg' 	: smartLogin.translates.EMAIL_INVALID
			},
			{
				'field' 	: $$('#smartlogin .password.reg-input')[0],
				'validator' : 'isPasswordLengthValid',
				'errorMsg' 	: smartLogin.translates.SHORT_PASSWORD
			},
			{
				'field' 		: $$('#smartlogin .confirmpassword.reg-input')[0],
				'fieldConfirm' 	: $$('#smartlogin .password.reg-input')[0],
				'validator' 	: 'passwordsAreMatch',
				'errorMsg' 		: smartLogin.translates.PASSWORDS_NOT_MATCH
			}
		];
		return smartLogin.validate(list);
	},

	reloadCaptcha: function() {
		var captchaElements = smartLogin.openWindow.select('.reload-captcha');
		if (captchaElements.length) {
			for (var i = 0; i < captchaElements.length; i++) {
				var reloadCaptcha = captchaElements[i].onclick;
				reloadCaptcha(null);
			}
		}
	},

	ajaxRegister: function() {
		if (smartLogin.validateRegisterFields() && smartLogin.validateForm('smartlogin-register-form')) {
			if (!smartLogin.openWindow.hasClassName('custom-reg-form')) {
				var subscribed = 'false';
				if ($$('#smartlogin .issubscribed')[0].checked == true){
					subscribed= 'true';
				}
			} else {
				setTimeout(smartLogin.calculateElementOffset.bind(this, smartLogin.openWindow), 100);
			}
			smartLogin.loadingMask('reg');
			var url = smartLogin.callUrlCreate;
			url += (url.indexOf('?') == -1) ? '?' : '&';
			url += $('smartlogin-register-form').serialize();

			JSONP(url, function () {});
		}
		if (smartLogin.openWindow.hasClassName('custom-reg-form')) {
			setTimeout(smartLogin.calculateElementOffset.bind(this, smartLogin.openWindow), 100);
		}
	},

	validateForm: function(formId) {
		var validator = new Validation(formId);
		if (validator.validate()) {
			return true;
		} else {
			if (smartLogin.openWindow.hasClassName('custom-reg-form')) {
				return false;
			}
			var form = $(formId);
			form.select('.validation-advice').each(function(elm) {
				elm.remove()
			});
			form.select('.field-error').each(function(elm) {
				if (!elm.select('.validation-failed').length) {
					elm.removeClassName('field-error');
				}
			});
			form.select('.validation-failed').each(function(elm) {
				elm.removeClassName('validation-failed');
				elm.up().addClassName('field-error');
			});
			this.displayError(this.translates.ALL_FIELDS_ARE_REQUIRED);
			return false;
		}
	},

	validateRetrieveFilds: function(){
		var list = [
			{
				'field' 	: $$('#smartlogin .emailretrieve.ret-input')[0],
				'validator' : 'requiredFieldIsFilled',
				'errorMsg' 	: smartLogin.translates.EMAIL_IS_REQUIRED
			},
			{
				'field' 	: $$('#smartlogin .emailretrieve.ret-input')[0],
				'validator' : 'isEmailValid',
				'errorMsg' 	: smartLogin.translates.EMAIL_INVALID
			}
		];
		return smartLogin.validate(list);
	},

	ajaxRetrieve: function(){
		if (smartLogin.validateRetrieveFilds()){
			smartLogin.loadingMask('ret');
			var url = smartLogin.callUrlRetrieve;
			url += (url.indexOf('?') == -1) ? '?' : '&';
			url += 'email=' + $$('#smartlogin .emailretrieve.ret-input')[0].value;
			JSONP(url, function () {});
		}
	},

	closeError: function(){
		$$('#smartlogin .error')[0].setStyle({'width' : 'auto', 'marginLeft' : '0'});
		$$('#smartlogin .error')[0].hide();
		Element.hide($$('#smartlogin .loading-mask')[0]);
		//smartLogin.curentInput.focus();
	},

	initialize: function(){
		var blocks = $$('.itoris-smartlogin-block');
		if (blocks.length > 1) {
			for (var i = 1; i < blocks.length; i++) {
				blocks[i].remove();
			}
		}
		this.hideMasks();
		if ($('smartlogin').parentNode.tagName.toLowerCase() != 'body') {
			$$('body')[0].appendChild($('smartlogin'));
		}
		Event.observe($$('#smartlogin .loginbutton.btn-submit')[0], 'click', this.ajaxLogin);
		if ($$('#smartlogin .registerbutton.btn-submit')[0]) {
			Event.observe($$('#smartlogin .registerbutton.btn-submit')[0], 'click', this.ajaxRegister);
		}
		Event.observe($$('#smartlogin .retrievebutton.btn-submit')[0], 'click', this.ajaxRetrieve);
		Event.observe($$('#smartlogin .dialog-log.smartform')[0], 'keypress',
				function(event){
					var e = (window.event)? window.event: event;
					if(e.keyCode == 13){
						smartLogin.ajaxLogin();
					}
				});
		if ($$('#smartlogin .dialog-reg.smartform')[0]) {
			Event.observe($$('#smartlogin .dialog-reg.smartform')[0], 'keypress',
					function(event){
						var e = (window.event)? window.event: event;
						if(e.keyCode == 13){
							smartLogin.ajaxRegister();
						}
					});
		}
		Event.observe($$('#smartlogin .dialog-ret.smartform')[0], 'keypress',
				function(event){
					var e = (window.event)? window.event: event;
					if(e.keyCode == 13){
						smartLogin.ajaxRetrieve();
					}
				});
		Event.observe($$('#smartlogin .a-log-in.log-in')[0], 'click', function(event){
			smartLogin.showStep =0;
			smartLogin.hideSmart($$('#smartlogin .dialog-log')[0],'register',1);
			Event.stop(event);
		});
		Event.observe($$('#smartlogin .forgot')[0], 'click', function(event){
			smartLogin.showStep =0;
			smartLogin.hideSmart($$('#smartlogin .dialog-log')[0],'retrieve',1);
			Event.stop(event);
		});
		if ($$('#smartlogin .a-register')[0]) {
			Event.observe($$('#smartlogin .a-register')[0],'click', function(event){
				smartLogin.showStep =0;
				smartLogin.hideSmart($$('#smartlogin .dialog-reg')[0],'login',1);
				Event.stop(event);
			});
		}
		Event.observe($$('#smartlogin .a-retrieve')[0], 'click', function(event){
			smartLogin.showStep =0;
			smartLogin.hideSmart($$('#smartlogin .dialog-ret')[0],'login',1);
			Event.stop(event);
		});
		Event.observe($$('#smartlogin .close-error')[0], 'click', smartLogin.closeError);
		Event.observe($$('#smartlogin .close.close-error')[0], 'click', function(event){
			smartLogin.hideSmartDialogs();
		});
		Event.observe($$('#smartlogin .close.close-error')[1], 'click', function(event){
			smartLogin.hideSmartDialogs();
		});
		if ($$('#smartlogin .close.close-error')[2]) {
			Event.observe($$('#smartlogin .close.close-error')[2], 'click', function(event){
				smartLogin.hideSmartDialogs();
			});
		}
		Event.observe($$('#smartlogin .overlay-modal')[0], 'click', function(event){
			$$('#smartlogin .error')[0].style.display != 'none' ? smartLogin.closeError() : smartLogin.hideSmartDialogs();
		});
	}
}

function showSmartLoginDialog(){
	return smartLogin.showSmartLoginDialog();
}

function showSmartRegisterDialog(){
	return smartLogin.showSmartRegisterDialog();
}

function  hideSmartDialog() {
	return smartLogin.hideSmartDialogs();
}

var JSONP = function(global){
    function JSONP(uri, callback) {
        function JSONPResponse() {
            try {
				delete global[src]
			} catch(e) {
                global[src] = null
            }
            documentElement.removeChild(script);
            callback.apply(this, arguments);
        }
        var src = prefix + id++;
        var script = document.createElement("script");
        global[src] = JSONPResponse;
        documentElement.insertBefore(
            script,
            documentElement.lastChild
        ).src = uri;
    }
    var id = 0;
    var prefix = "__JSONP__";
    var document = global.document;
    var documentElement = document.documentElement;
    return JSONP;
}(this);
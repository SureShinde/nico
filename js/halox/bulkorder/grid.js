/**
 * bulk order javascript grid 
 */

if(typeof window.jQuery !== undefined && typeof window.jQuery.ui !== undefined){

	(function($){


		$.widget('halox.bulkOrderGrid', {
			
			options: {
				horizontalTabId	: 'horizontal-tabs',
				verticalTabId	: 'vertical-tabs',
				loaderClass: 'loader',
				loaderContainerClass: 'loader-overlap',
				loaderContainerId: 'grid-loading',
				gridContainer: 'bulkorder-grid', 
				disableSubmitOnEnter: 0,
				dialogConfig: {
					autoOpen: false,
	      			modal: true,
	      			appendTo: '#bulkordergrid-dialog',
	      			resizable: false
				}
			},

			_create: function(){
				
				if(this.options.baseCategoryId === undefined){
					console.warn('bulk order form base category id is not set.');
					return;
				}

				if(this.options.formId === undefined){
					console.warn('bulk order form id is not set.');
					return;
				}

				if(this.options.isMultiStep === undefined){
					console.warn('horizontal tabs steps not found.');
					return;
				}

				if(this.options.loaderImageUrl === undefined){
					console.warn('No loader image has been provided.');
					return;
				}

				this._initDialog();

				this._initHorizontalTabs();

				this.rowTitleData = {};

				if(this.options.disableSubmitOnEnter !== undefined && this.options.disableSubmitOnEnter){
					$('#' + this.options.formId).on('keypress', $.proxy(this._disableSubmitOnEnter, this));	
				}

				this._bindAddToCartOnSubmit();
						
			},

			_bindAddToCartOnSubmit: function(){

				$('#' + this.options.formId).on('submit', $.proxy(this.submitForm, this));
				
			},

			_enableAddToCartButton: function(){
				
				if($('#' + this.options.formId).find(':submit').prop('disabled')){
					$('#' + this.options.formId).find(':submit').prop('disabled', false).removeClass('disable-button');		
				}
				
			},

			_disableAddToCartButton: function(){
				
				$('#' + this.options.formId).find(':submit').prop('disabled', true).addClass('disable-button');
				
			},

			_disableSubmitOnEnter: function(event){
				if((event.keyCode || event.charCode || event.which) === 13){
					event.preventDefault();		
				}
			},

			_initCellPrices: function(){
				this.cellAggregates = {};
			},

			_initHorizontalTabs: function(){
				
				tabConfigurationData = {
					beforeLoad: this.onBeforeTabLoad.bind(this),
					beforeActivate: this.onBeforeTabActivate.bind(this),
					activate: this.onTabActivate.bind(this),
					load: this.onHorizontalTabLoad.bind(this)
				};

				if(this.options.preConfiguredData !== undefined && Object.keys(this.options.preConfiguredData).length){
					
					activeTabId = this.options.preConfiguredData.horizontal_active_id;
					activeTabIndex = $('#tab-item-' + activeTabId).attr('data-tab-index');
					
					tabConfigurationData.disabled = true;
					tabConfigurationData.active = activeTabIndex;

				}

				$('#' + this.options.horizontalTabId).tabs(tabConfigurationData);

				return this;

			},

			initVerticalTabs: function(){

				var verticalTabsContainerId = this.options.verticalTabId + '-' + this.currentHorizontalTabId;
				
				tabConfigurationData = {
					beforeLoad: this.onBeforeTabLoad.bind(this),
					beforeActivate: this.onBeforeTabActivate.bind(this),
					activate: this.onTabActivate.bind(this),
					load: this.onVerticalTabLoad.bind(this),
				};

				if(this.options.preConfiguredData !== undefined && Object.keys(this.options.preConfiguredData).length){
					activeTabId = this.options.preConfiguredData.vertical_active_id;
					activeTabIndex = $('#vertical-tab-'  + this.options.preConfiguredData.horizontal_active_id + '-' + activeTabId).attr('data-tab-index');
					
					tabConfigurationData.disabled = true;
					tabConfigurationData.active = activeTabIndex;

				}

				var tabItemInstance = $('#' + verticalTabsContainerId + ' ul li');
				var verticalTabWidth = (100 / tabItemInstance.length) + '%';

				tabItemInstance.css({
					width: verticalTabWidth
				});

				tabItemInstance.on('click', function(event){
					
					event.preventDefault();
					
					tabConfigurationData.active = $(this).attr('data-tab-index');
					
					$('#' + verticalTabsContainerId).tabs(tabConfigurationData);

				});

				if(this.options.preConfiguredData !== undefined 
						&& Object.keys(this.options.preConfiguredData).length
				){
					$('#' + verticalTabsContainerId).tabs(tabConfigurationData);	
				}

			},

			onBeforeTabLoad: function(event, ui){
				
				/*if(!this.options.isMultiStep){
					$(ui.panel).parent().parent().parent().find('.step-title > span').hide();
				}*/

				this._resetRowTitleData();

				this._resetGrid();

				this.showAddToCartLoader();

				this._unbindUnloadAlert();

				//if session has expired redirect the user to login screen
				ui.jqXHR.error(this.onAjaxRequestError.bind(this));
			},

			onBeforeTabActivate: function(event, ui){

				instance = this;

				var oldConfig = {};
				var oldItemConfig = {};
				var newConfig = {};
				var newItemConfig = {};

				
				ui.oldPanel.find('input[name^="super_attribute"]').each(function(key, superAttribute){
					
					var nameAry = superAttribute.name.split('[');

					var productId = nameAry[2].substr(0, nameAry[2].length-1);
					var attributeId = nameAry[3].substr(0, nameAry[3].length-1);

					if(oldItemConfig[productId] == undefined){
						oldItemConfig[productId] = {};
						oldItemConfig[productId][attributeId] = superAttribute.value;
					}else{
						prodConfig = oldItemConfig[productId];
						prodConfig[attributeId] = superAttribute.value;
						oldItemConfig[productId] = prodConfig;
					}

					if($(this).hasClass('qty') && superAttribute.value){
						oldConfig[productId] = oldItemConfig[productId];
					}	
					
				});

				if(Object.keys(oldConfig).length > 0){
					
					// If event is not triggered by user
					if ( ! this.bulkOrderDialog.data("confirmed")) { 
						
						// prevent switching tabs
						event.preventDefault(); 

						this.openDialog({
							//title: 'Confirm',
							html: '<p>Do you want to leave this page without adding the selected products to your cart?</p>',
							dialogClass: 'center',
							buttons: [
								{
									text: 'Leave Page',
									click: function(){
										var ui = $(this).data("ui");
										var tabId = $(this).data("tabId"); 
										
										// if user clicks yes, change the stored data to true to avoid re-opening dialog
										$(this).dialog('close').data("confirmed", true);
										
										$("#"+ tabId).tabs("option", "active", ui.newTab.index());	
									}	
									
								},
								{
									text: 'Stay on Page',
									class: 'pull-left',
									click: function(){
										$(this).dialog('close').data("confirmed", false);	
									}	
								}
							],
							data: {
								tabId: event.target.id,
								ui: ui
							}
						});

					}
				}

			},

			onTabActivate: function(event, ui){
				
				this.bulkOrderDialog.data("confirmed", false);

				ui.oldPanel.find('input').prop('disabled', true);
				ui.newPanel.find('input').prop('disabled', false);

			},

			onHorizontalTabLoad: function(event, ui){
				
				if(this.options.preConfiguredData !== undefined && Object.keys(this.options.preConfiguredData).length <= 0){
					this._disableAddToCartButton();	
				}
				
				this.hideAddToCartLoader();

				this.currentHorizontalTabId = ui.tab.attr('id').split('-')[2];

				this._disableAllInputs();

				this.initVerticalTabs();

			},

			_disableAllInputs: function(){

				$("#" + this.options.gridContainer + " input").prop('disabled', true);

			},

			_validateQty: function(element){

				var stockOptionId = element.name.replace('qty', 'stock');
				var availableQty = $('input[type="hidden"][name="' + stockOptionId + '"]').val();
				
				var isQtyValid = true;

				if(parseFloat($(element).val()) > parseFloat(availableQty)){
					
					this.openDialog({
						html: '<p>An E-liquid quantity entered is invalid, or this product may be low on stock.</p>',
						dialogClass: 'center',
						buttons: {
							Ok: function() {
							  $(this).dialog('close');
							}	
						}
					});

					$(element).addClass('has-error')
						.removeClass('qty-passed');

					isQtyValid = false;

				}else{
					
					$(element).removeClass('has-error');
					if(parseFloat($(element).val()) > 0){
						$(element).addClass('qty-passed');
					}else{
						$(element).removeClass('qty-passed');
					}

					isQtyValid = true;	
						
				}

				return isQtyValid;

			},

			_getTotal: function(entityType, productIndex, rowIndex){

				var rowTotal = 0;
				var rowObj = {};

				if(rowIndex === undefined){
					rowObj = this.cellAggregates[productIndex];
				}else{
					rowObj = this.cellAggregates[productIndex][rowIndex]
				}

				$.each(rowObj, function(key, rowData){
					
					if(typeof rowData == 'object' && rowData[entityType] === undefined){
						
						$.each(rowData, function(itemKey, itemData){
							switch(entityType){
								case 'qty':
									rowTotal += parseInt(itemData[entityType]);
									break;	
								case 'row_total':
									rowTotal += parseFloat(itemData[entityType]);
									break;
							}
							
						});

					}else{
						switch(entityType){
							case 'qty':
								rowTotal += parseInt(rowData[entityType]);
								break;	
							case 'row_total':
								rowTotal += parseFloat(rowData[entityType]);
								break;
						}
					}

				});

				return entityType == 'qty' ? rowTotal : rowTotal.toFixed(2);
			},

			_updateQtysAndTotals: function(element){

				var qtyElementName = $(element).attr('name').split('[')[1];

				var productIndex = qtyElementName.substr(0, qtyElementName.indexOf(']'));

				var cellIndex = $(element).parent().attr('id').split('-')[1]; 
				
				var rowIndex = $(element).parent().parent().attr('id').split('-')[2];

				var rowQtyElement = $(element).parent().parent().find('.row-qty');

				var rowUnitPriceElement = $(element).parent().parent().find('.row-unit-price');

				var rowTotalElement = $(element).parent().parent().find('.row-total');

				var subtotalElement = $('#' + this.options.gridContainer + ' #subtotal-row td.subtotal-value span');

				var bottleCountElement = $('#' + this.options.gridContainer + ' #bottle-count-row td.subtotal-value span');
				
				if(this.cellAggregates[productIndex] === undefined 
					|| this.cellAggregates[productIndex][rowIndex] === undefined 
					|| this.cellAggregates[productIndex][rowIndex][cellIndex] === undefined
				){
					
					if(this.cellAggregates[productIndex] === undefined){
						this.cellAggregates[productIndex] = {};
					}

					if(this.cellAggregates[productIndex][rowIndex] === undefined){
						this.cellAggregates[productIndex][rowIndex] = {};
					}

				}

				rowUnitPrice = parseFloat(rowUnitPriceElement.attr('data-price'))
					? parseFloat(rowUnitPriceElement.attr('data-price')) : 0.00;
				
				rowQty = parseInt($(element).val()) 
					? parseInt($(element).val()) : 0;

				this.cellAggregates[productIndex][rowIndex][cellIndex] = {
					qty: rowQty,
					row_total: (rowUnitPrice * rowQty)
				};
				
				rowQtyElement.html(this._getTotal('qty', productIndex, rowIndex));

				bottleCountElement.html(this._getTotal('qty', productIndex));

				rowTotalElement.html(this.options.currencySymbol + this._getTotal('row_total', productIndex, rowIndex));

				subtotalElement.html(this.options.currencySymbol + this._getTotal('row_total', productIndex));

			},

			onChangeInputQty: function(event){

				instance = event.data.instance;

				if(instance._validateQty(event.target)){
					instance._updateQtysAndTotals(event.target);	
				}

				if($(event.target).val() > 0){
					instance._enableAddToCartButton();	
				}
				

			},

			_initFixTableCells: function(){
				if($.isFunction($.fn.tableHeadFixer)){
					$(".scrolltable").tableHeadFixer({
						"z-index": 9,
                    	"foot": true,
                    	"left": 1
					});

					$('.scrolltable_base').prepend($('<div>').css({
						'position': 'absolute',
						'background-color': '#fff',
						'z-index': '11',
						'width' : '100%'
					}).html('&nbsp;'));  
				}
			},

			_initQtyElements: function(ui){

				instance = this;

				var qtyElement = ui.panel.find("input.qty");

				qtyElement.on("change", {instance: this}, this.onChangeInputQty);
				qtyElement.on("keydown", function(e){
					-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault();
				});

				qtyElement.each(function(i, elem){
					instance._updateQtysAndTotals(elem);
				});

			},

			onVerticalTabLoad: function(event, ui){

				instance = this;

				this._initCellPrices();

				this._bindRowTitleInfo();	
				
				this.hideAddToCartLoader();

				this._initQtyElements(ui);

				this._initFixTableCells();

				

				//push all the qtys to inititalQtyString global string defined in wholesaleJS.phtml
				var initialInputQtysPanel = '';

				ui.panel.find('input.qty').each(function(i,qtyElement){
					if($(qtyElement).val()){
						initialInputQtysPanel += $(qtyElement).val(); 	
					}
					
				});

				if(typeof initialQtyString != 'undefined'){
					initialQtyString = initialInputQtysPanel;
				}

			},

			showAddToCartLoader: function(){
				
				var overlay = $('<div></div>').attr('class', this.options.loaderContainerClass)
								.attr('id', this.options.loaderContainerId);

				var loader = $('<div>').attr('class', this.options.loaderClass);

				/*$('<img>').attr('src', this.options.loaderImageUrl)
					.css({'vertical-align':'middle'})
					.appendTo(loader);*/

                $('<i>').attr('class', 'ajax-loader large animate-spin').appendTo(loader);

                var loaderContainer = $('<div>').attr('class', this.options.loaderContainerClass)
						.css({opacity: '1', background:'transparent'});	

				loader.appendTo(loaderContainer);
				
				loaderContainer.css({
					left: '0px',
					'z-index': '9999'
				});

				overlay.css({
					left: '0px',
					'z-index': '9999'
				});

				overlay.appendTo('body');
				loaderContainer.appendTo('body');
				
			},

			hideAddToCartLoader: function(){

				$('.' + this.options.loaderContainerClass).remove();
			
			},

			validateAndPrepareQtys: function(formData, parentProductId){

				var postData = {};

				$.each(formData, function(key, data){
					
					var isQtyValid = data.qty != undefined && parseFloat(data.qty) > 0;

					if(isQtyValid){
						postData[key] = data;
					}else if(data.quote_item != undefined && data.quote_item.length){
						data.is_delete = 1;
						postData[key] = data;
					}

					if(data.qty != undefined && parseFloat(data.qty) > 0){
						postData[key] = data;
					}else if(parseFloat(data.qty) == 0){
						data.is_delete = 1;
						postData[key] = data;
					}
				});

				var dataHasError = {};

				$.each(postData, function(key, data){
					
					var qtyElement = $('input[name="super_attribute[' + parentProductId + ']['+ key +'][qty]"]');
					
					if(parseFloat(data.qty) > parseFloat(data.stock)){
						
						if(dataHasError[parentProductId] === undefined){
							dataHasError[parentProductId] = {};
						}else{
							errorData = dataHasError[parentProductId];
							errorData[key] = data;
							dataHasError[parentProductId] = errorData; 
						}
						
						
						dataHasError[parentProductId][key] = data;	
						
						qtyElement.addClass('has-error');

					}else{
						
						if(dataHasError[parentProductId] !== undefined && 
							dataHasError[parentProductId][key] !== undefined){
							delete dataHasError[parentProductId][key];	
						}
						
						if(qtyElement.hasClass('has-error')){
							qtyElement.removeClass('has-error');	
						}

					}

				});

				if($.isEmptyObject(dataHasError)){
					return postData;
				}

				return false;

			},

			_bindScroll: function(dialogInstance){

				$(window).on('scroll', function(){
					$(dialogInstance).parent().parent().position({
						my: "center",
						at:"center",
						of: $(this)
					});
				});
			},

			_initDialog: function(config){
				
				this.bulkOrderDialog = '';

				if(config === undefined){
					config = this.options.dialogConfig;
				}

				if( ! this.bulkOrderDialog){
					this.bulkOrderDialog = $('<div>').dialog(config);
				}

				this._bindScroll(this.bulkOrderDialog);
				
				return this;
			},

			openDialog: function(data){
				
				if(data.title != undefined){
					this.bulkOrderDialog.dialog('option', 'title', data.title);
				}
				
				if(data.html != undefined){
					this.bulkOrderDialog.html(data.html);
				}

				if(data.buttons != undefined){
					this.bulkOrderDialog.dialog('option', 'buttons', data.buttons);	
				}

				if(data.closeOnEscape != undefined){
					this.bulkOrderDialog.dialog('option', 'closeOnEscape', data.closeOnEscape);		
				}

				if(data.dialogClass != undefined){
					this.bulkOrderDialog.dialog('option', 'dialogClass', data.dialogClass);		
				}

				if(data.data !== undefined){
					this.bulkOrderDialog.data(data.data);
				}else{
					this.bulkOrderDialog.data('ui', '');
					this.bulkOrderDialog.data('tabType', '');
					this.bulkOrderDialog.data('confirmed', '');
				}

				this.bulkOrderDialog.dialog("open");
				
				return this.bulkOrderDialog;	
				
			},

			_resetGrid: function(){

				$('#' + this.options.gridContainer + ' input.qty').removeClass('qty-passed').val('');
				
				this._resetQtyNPrices();
				this._initCellPrices();

			},

			_resetQtyNPrices: function(){
				
				$('#' + this.options.gridContainer + ' tr td .row-qty').html('0');
				$('#' + this.options.gridContainer + ' tr td .row-total').html(this.options.currencySymbol + '0.00');

				$('#' + this.options.gridContainer + ' tr td.subtotal-value').html(this.options.currencySymbol + '0.00');
			},

			navigateTo: function(pageCode){
						
				switch(pageCode){
					case 'cart': window.location = this.options.cartUrl; break;
					case 'checkout' : window.location = this.options.checkoutUrl; break;
				}

				window.location = this.options.cartUrl;
			},

			_updateSectionsAfterAddToCart: function(responseData){

				if(this.options.updateSectionsAfterAddToCart === undefined){
					return this;
				}

				$.each(this.options.updateSectionsAfterAddToCart, function(responseKey, sectionSelectors){
					if(typeof sectionSelectors == 'string'){
						if($(sectionSelectors).length > 0 && responseData.responseKey != undefined){
							$(sectionSelectors).replaceWith(responseData[responseKey]);
						}
					}else if(typeof sectionSelectors == 'object' && Object.keys(sectionSelectors).length > 0){
						for(i= 0; i < Object.keys(sectionSelectors).length; i++){
							var selector = sectionSelectors[i];
							if($(selector).length !== undefined && $(selector).length > 0){
								$(selector).replaceWith(responseData[responseKey]);
							}
						}
					}
				});

			},

			_unbindUnloadAlert: function(){
				//remove onbeforeunload handlers
				window.onbeforeunload = '';
			},

			onAddToCartSuccess: function(data, status, jqXHR){

				var qtyBoxName = '',
					qtyBoxElem = '';	

				if(typeof data.parent_prod_id !== 'undefined' 
					|| typeof data.simple_prod_id !== 'undefined'
				){
					qtyBoxName = 'super_attribute[' + data.parent_prod_id + '][' 
						+ data.simple_prod_id + '][qty]';
					
					qtyBoxElem = $('input[name="'+ qtyBoxName  + '"]');
				}
				

				if(data.status == 'SUCCESS'){
					
					this._unbindUnloadAlert();

					this._updateSectionsAfterAddToCart(data);

					$('input.qty').css('border-color', '#CCC');
					
					var dialogButtons = [
						{
							text: 'Continue',
							click: function(){
								$(this).dialog('close');		
							},
							css: {
								float: 'left'
							}
						},
						{
							text: 'Go to cart page',
							click: function(){
								instance.navigateTo('cart');
							}
						}
					];

					this.openDialog({
						html: '<p>'+ data.message +'</p>',
						width: 500,
						dialogClass: (data.read_only != undefined && data.read_only) ? 'no-close center' : 'center',
						closeOnEscape: (data.read_only != undefined && data.read_only) ? false : true,
						buttons: (data.read_only != undefined && data.read_only) ? {} : dialogButtons
					});

					if(data.redirect_url !== undefined){
						window.location = data.redirect_url;
						return;
					}

					if(data.reset_grid != undefined && data.reset_grid){
						this._resetGrid();		
					}	

				}else if(data.status = 'ERROR'){
					
					if(qtyBoxElem.length > 0){
						qtyBoxElem.css('border-color', 'RED');
					}

					this.openDialog({
						//title: 'Request Error',
						html: '<p>'+ data.message +'</p>',
						dialogClass: 'center',
						buttons: {
							Ok: function() {
							  $(this).dialog('close');
							}
						}
					});
				}

			},

			onAjaxRequestError: function(jqXHR, status, error){

				var errorMsg = '',
					isReadOnly = '',
					redirectUrl = '',
					simpleProdId = '',
					parentProdId = '';
					qtyBoxName = '',
					qtyBoxElem = '';	

				if(jqXHR.responseJSON !== undefined && jqXHR.responseJSON.message !== undefined){
					
					errorMsg = jqXHR.responseJSON.message;
					isReadOnly = jqXHR.responseJSON.read_only;
					redirectUrl = jqXHR.responseJSON.location;

					var parentProdInError = jqXHR.responseJSON.parent_prod_id !== undefined 
						? jqXHR.responseJSON.parent_prod_id : '';
					var childProdInError = jqXHR.responseJSON.simple_prod_id !== undefined
						? jqXHR.responseJSON.simple_prod_id : '';;

					if(parentProdInError && childProdInError){
						qtyBoxName = 'super_attribute[' + parentProdInError + '][' 
							+ childProdInError 
							+ '][qty]';

						qtyBoxElem = $('input[name="'+ qtyBoxName  + '"]');
						qtyBoxElem.css('border-color', 'RED');
					}
				
				}else if(jqXHR.responseText.length > 0){
					
					errorMsg = error + ': ' + jqXHR.responseText;  	

				}else{
					
					errorMsg = error;  	
				}
				
				this.openDialog({
					html: '<p>'+ errorMsg +'</p>',
					dialogClass: 'center',
					dialogClass: (isReadOnly != undefined && isReadOnly) ? 'no-close center' : 'center',
					closeOnEscape: (isReadOnly != undefined && isReadOnly) ? false : true,
					buttons: (isReadOnly != undefined && isReadOnly) ? {} : {
						Ok: function() {
						  $(this).dialog('close');
						}
					}
				});

				if(redirectUrl && redirectUrl.length){
					window.location = redirectUrl;
				}
			},

			_checkIfQuoteItemExists: function(elemKey, targetFormData){
				
				var hasQuoteItem = 0;
				$.each(targetFormData, function(i, formElement){
					if(formElement.name == elemKey 
						&& formElement.value.length > 0
					){
						hasQuoteItem = 1;
						return false;
					}
				});

				return hasQuoteItem;
			},

			/**
             * use itemStockData array to findout invalid qtys in formdata and delete them before
             * making ajax request 
			 */
			_prepareSubmitData: function(targetFormData){

				var instance = this;

				var validElementKeys = [],
					validTargetFormData = [];

				$.each(targetFormData, function(i, formElement){
					
					var keyParts = formElement.name.match(/super_attribute\[(\w+)\]\[(\w+)\]\[(\w+)\]/);
					
					if(keyParts === null){
						validTargetFormData.push(formElement);
						return;
					}

					if(/qty/.test(formElement.name)){
						
						var elemHasValidQty = parseInt(formElement.value) > 0;
						
						var itemKeyPrefix = 'super_attribute[' 
								+ keyParts[1] + '][' + keyParts[2] + ']';

						var quoteItemKey = itemKeyPrefix + '[quote_item]';

						var itemIsInEditMode = instance._checkIfQuoteItemExists(quoteItemKey, targetFormData); 
						
						if(elemHasValidQty){
							
							validElementKeys.push(itemKeyPrefix);

						}else if(itemIsInEditMode){

							validElementKeys.push(itemKeyPrefix);							
						}
					}
				});


				$.each(targetFormData, function(i, formElement){
					$.each(validElementKeys, function(j, elemKey){
						elemKeyReg = new RegExp('^' + elemKey.replace(/(\W)/g, '\\$1'));
						if(elemKeyReg.test(formElement.name)){

							/**
							 * is element is qty element and qty value is not valid
							 * that means this item has been deleted by user so insert a is_delete
							 * key for the same item key so that it can be deleted properly
							 */
							if(/qty/.test(formElement.name)){
								
								var isValidQtyValue = parseInt(formElement.value) > 0;
								if(!isValidQtyValue){
									
									formElement.value = 0;
									
									deleteFormElement = {
										'name' : elemKey + '[is_delete]',
										'value' : 1
									};

									validTargetFormData.push(deleteFormElement);	
								}
								
							}

							validTargetFormData.push(formElement);
						}
					});
				});

				return validTargetFormData;

			},

			submitForm: function(event){
				
				event.preventDefault();	

				var instance = this;
				
				var formDataObj = {};
				var postData = {};

				//it will be used later to remove invalid qtys from FormData object
				var itemStockData = {};

				var mainProductId = '';
				var parentCategoryId = '';
				var baseCategoryId = '';

				var submitUrl = $(event.target).attr('action');

				var formData = $(event.target).serializeArray();

				$.each(formData, function(i, field){
					
					if(field.name == 'horizontal_active_id'){
						parentCategoryId = field.value; 
						return;
					}
					
					if(field.name == 'vertical_active_id'){
						mainProductId = field.value;
						return;
					}

					if(field.name == 'base_category_id'){
						baseCategoryId = field.value;
						return;
					}

					var fieldNames = field.name.split('[');

					if(fieldNames[2] == undefined || fieldNames[3] == undefined){
						return;
					}

					var productId = fieldNames[2].substr(0, fieldNames[2].length-1);
					var attributeId = fieldNames[3].substr(0, fieldNames[3].length-1);

					if(itemStockData[mainProductId] === undefined){
						itemStockData[mainProductId] = {};
						itemStockData[mainProductId][productId] = [];
					}

					if(itemStockData[mainProductId][productId] === undefined){
						itemStockData[mainProductId][productId] = [];
					}

					itemStockData[mainProductId][productId].push(attributeId);

					if(formDataObj[productId] == undefined){
						formDataObj[productId] = {};
						formDataObj[productId][attributeId] = field.value;	
					}else{
						prevConfig = formDataObj[productId];
						prevConfig[attributeId] = field.value;
						formDataObj[productId] = prevConfig;
					}
				});

				if(baseCategoryId == '' || mainProductId == '' || parentCategoryId == ''){
					
					this.openDialog({
						html: '<p>'+ 'Faulty configuration: Main Product Id missing in grid.' +'</p>',
						dialogClass: 'center',
						buttons: {
							Ok: function() {
							  $(this).dialog('close');
							}
						}
					});
					
					return false;
				}

				//qty validation
				var postData = instance.validateAndPrepareQtys(formDataObj, mainProductId);
				if(postData === false){
					
					this.openDialog({
						html: '<p>'+ 'An E-liquid quantity entered is invalid, or this product may be low on stock.' +'</p>',
						dialogClass: 'center',
						buttons: {
							Ok: function() {
							  $(this).dialog('close');
							}
						}
					});

					return false;
				}

				if($.isEmptyObject(postData)){
					
					this.openDialog({
						html: '<p>'+ 'No product was selected to add to cart.' +'</p>',
						dialogClass: 'center',
						buttons: {
							Ok: function() {
							  $(this).dialog('close');
							}
						}
					});

					return false;
				}

				
				submitData = this._prepareSubmitData(formData);

				submitData.push({
					'name' : 'isAjax',
					'value' : 1
				});

				$.ajax({
					url: submitUrl,
					data: submitData,
					dataType: 'JSON',
					method: 'POST',
					beforeSend: function(jqXHR, settings){
						instance.showAddToCartLoader();
					},
					complete: function(jqXHR, status){
						instance.hideAddToCartLoader();
					},
					success: instance.onAddToCartSuccess.bind(instance),
					error: instance.onAjaxRequestError.bind(instance)
				});
			},

			initRowTitleData: function(rowTitleData){
				this.rowTitleData[rowTitleData.rowId] = rowTitleData;
				delete this.rowTitleData[rowTitleData.rowId].rowId;
			},

			_resetRowTitleData: function(){
				this.rowTitleData = {};
			},

			_bindRowTitleInfo: function(){
				
				if( this.rowTitleData !== undefined){
						
					var instance = this;
				
					$.each(this.rowTitleData, function(rowId, rowData){

						var rowCell = $('#' + instance.options.gridContainer + ' #' + rowId + ' td:first-child'); 
						var rowSpan = $('#' + instance.options.gridContainer + ' #' + rowId + ' td:first-child span');
						
						var titleDescriptionModal = $('<div>').attr('id', rowId + '-modal')
							.attr('class', 'modal fade')
							.attr('role', 'dialog');

						var modalDialog = $('<div>').attr('class', 'modal-dialog modal-lg');
						var modalContent = 	$('<div>').attr('class', 'modal-content');
						var modalHeader = $('<div>').attr('class', 'modal-header');
						
						var modalBody = $('<div>').attr('class', 'modal-body');
                                                var modalFooter = $('<div>').attr('class', 'modal-footer');
                                                
						var bodyContent = $('<div>').attr('class', 'media')
							.append('<div class="media-left"><a href="#"><img class="media-object" src="'+ rowData.image +'"></a></div>')
							.append('<div class="media-body">'+ rowData.description +'</div>');
						


						var buttonHeader = $('<button>').attr('type', 'button')
							.attr('class', 'close')
							.attr('data-dismiss', 'modal')
							.attr('aria-label', 'Close')
							.html('<span aria-hidden="true">&times;</span>');

						
						bodyContent.appendTo(modalBody);
						buttonHeader.appendTo(modalHeader);
						modalHeader.appendTo(modalContent);
						modalBody.appendTo(modalContent);
						modalContent.appendTo(modalDialog);
                                                modalFooter.appendTo(modalContent);
						modalDialog.appendTo(titleDescriptionModal);

						titleDescriptionModal.appendTo(rowCell);

						if(rowData.title.length > 0){
							rowSpan.attr('title', rowData.title);	
						}
						
						rowSpan.on('click', function(event){
                            $('#' + instance.options.gridContainer + ' #' + rowId + '-modal').modal('toggle');
						});

					});
				}else{
					console.warn('row title data not initialized.');
				}

			}

		});


	})(jQuery);

}else{
	console.error('E-Liquid Order form required jQuery library and jQuery UI library to loaded on this page first.');
}


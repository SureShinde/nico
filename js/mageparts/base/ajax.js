/************************************
 * AJAX Controller
 ************************************/
var AJAX = (function() {
    var stack = [];
    var calling = false;

    /**
     * Makes an AJAX call.
     *
     * @param  {Object} obj The parameters.
     */
    var call = function(obj) {
        if (!calling) {
            calling = true;
            new Ajax.Request(obj.url, obj.options);
        }

        return AJAX;
    };

    return {
        /**
         * Makes an AJAX call for every item in the stack.
         */
        run: function() {
            var origOnComplete, callObj;

            if (!calling) {
                callObj = stack[0];

                if (callObj.options.onComplete) {
                    origOnComplete = callObj.options.onComplete;
                    callObj.options.onComplete = function(response) {
                        AJAX.applyHtmlUpdates(response);
                        origOnComplete(response);
                        calling = false;
                        stack.splice(0, 1);

                        if (stack.length > 0) {
                            AJAX.run();
                        }
                    }
                }

                call(callObj);
            }
        },

        /**
         * Adds an item to the stack.
         *
         * @param  {Object} obj
         */
        queue: function(obj) {
            stack.push(obj);

            return AJAX;
        },

        /**
         * Removes an item from the stack.
         *
         * @param  {Number} idx
         */
        remove: function(idx) {
            if (stack[idx]) {
                stack.splice(idx, 1);
            }

            return AJAX;
        },

        /**
         * Removes all of the items stored in the stack.
         */
        removeAll: function() {
            if (stack.length) {
                stack.splice(0, stack.length);
            }

            return AJAX;
        },

        /**
         * Apply HTML updates.
         *
         * @param transport
         */
        applyHtmlUpdates: function(transport)
        {
            var data = transport.responseJSON;

            if (data && data.htmlUpdates) {
                var updates = data.htmlUpdates;
                var idToUpdates = [];

                jQuery(updates).each(function() {
                    if (this.elId && this.html) {
                        if ($(this.elId)) {
                            var updateObj = this;
                            jQuery('#' + this.elId).stop().fadeOut({duration: 500, easing: 'easeInExpo', complete: function() {
                                jQuery(this).replaceWith(updateObj.html);

                                var newObj = jQuery('#' + updateObj.elId);
                                newObj.hide();

                                var tables = newObj.find('.data-table');

                                if (tables.length) {
                                    for (var i=0; i<tables.length; i++) {
                                        decorateTable(tables[i]);
                                    }
                                }

                                newObj.fadeIn({duration: 500, easing: 'easeOutExpo'});
                            }});
                        }
                    }
                });
            }
        }
    };
}());

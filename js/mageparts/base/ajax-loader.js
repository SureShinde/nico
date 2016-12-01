/************************************
 * AJAX Loader
 ************************************/
var AjaxLoader = Class.create({
    initialize: function(config) {
        var key;

        /**
         * The element that is used as a template for the loader.
         *
         * @type {Element}
         */
        this.template = null;

        /**
         * The loader.
         *
         * @type {Element}
         */
        this.element = null;

        /**
         * The element used to specify where the loader should show.
         *
         * @type {Element}
         */
        this.pointer = null;

        for (key in config) {
            if (this.hasOwnProperty(key)) {
                this[key] = config[key];
            }
        }

        if (this.template) {
            this.element = this.template.clone(true);
            this.template.insert({before: this.element});
        }
    },

    /**
     * Shows the loader.
     */
    show: function() {
        var jqEl = jQuery(this.element);

        if (this.element && this.pointer) {
            MAGEPARTS.alignElement({
                element: this.element,
                anchor: this.pointer,
                align: ['center', 'under'],
                top: 60
            });

            jQuery(this.element).fadeIn(300).animate(
                {top: parseInt(this.element.style.top) - 50 + 'px'},
                {duration: 300, queue: false, easing: 'easeOutExpo'}
            );
        }
    },

    /**
     * Hides the loader.
     */
    hide: function() {
        if (this.element && this.pointer) {
            jQuery(this.element).fadeOut(200);
            //this.element.centerOfEl(this.pointer, 20, 0).stop().fadeOut(500);
        }
    },

    /**
     * Destroy function.
     */
    destroy: function() {
        var key;

        if (this.element.parentNode) {
            this.element.remove();
        }

        for (key in this) {
            if (this.hasOwnProperty(key)) {
                delete this[key];
            }
        }
    }
});

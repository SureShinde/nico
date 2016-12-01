var MessageBox = Class.create({
    initialize: function(config) {
        var key;

        /**
         * The element where messages are shown.
         *
         * @type {Element}
         */
        this.element = null;

        for (key in config) {
            if (this.hasOwnProperty(key)) {
                this[key] = config[key];
            }
        }
    },

    /**
     * Removes the messages.
     *
     * @return {MessageBox} Returns itself.
     */
    removeMessages: function() {
        while (this.element.firstChild) {
            this.element.firstChild.remove();
        }

        return this;
    },

    /**
     * Adds a message to the box.
     *
     * @param {String|HTMLElement} msg  The message.
     * @param {String} type The message type.
     * @return {MessageBox} Returns itself.
     */
    addMessage: function(msg, type) {
        var msgEl = new Element('div', {'class': type + ' msg'});
        msgEl.update(msg);

        this.element.insert({
            top: msgEl
        });

        setTimeout(function() {
            jQuery(msgEl).stop().fadeOut(500);
        }, 10000);

        return this;
    },

    /**
     * Adds an error message to the box.
     *
     * @param {String|HTMLElement} msg The message.
     * @return {MessageBox} Returns itself.
     */
    addError: function(msg) {
        return this.addMessage(msg, 'error');
    },

    /**
     * Adds a success message to the box.
     *
     * @param {String|HTMLElement} msg The message.
     * @return {MessageBox} Returns itself.
     */
    addSuccess: function(msg) {
        return this.addMessage(msg, 'success');
    },

    /**
     * Adds a notice message to the box.
     *
     * @param {String|HTMLElement} msg The message.
     * @return {MessageBox} Returns itself.
     */
    addNotice: function(msg) {
        return this.addMessage(msg, 'notice');
    }
});

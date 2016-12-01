/************************************
 * Dense Text
 ************************************/
var DenseText = Class.create({
    initialize: function(config) {
        var key, link, content, seperator, linkTxt;
        var txtProp = (document.body.textContent) ? 'textContent' : 'innerText';

        /**
         * If the content should be hideable.
         *
         * @type {Boolean}
         */
        this.hideable = false;

        /**
         * The element which contains the content, link, seperator etc.
         *
         * @type {Element}
         */
        this.element = null;

        /**
         * The link that shows/hides the content.
         *
         * @type {Element}
         */
        this.link = null;

        /**
         * States whether the content is hidden or not.
         *
         * @type {Boolean}
         */
        this.hidden = true;

        /**
         * The container for the expandable text.
         *
         * @type {Element}
         */
        this.content = null;

        /**
         * The text used as a seperator.
         *
         * @type {Element}
         */
        this.seperator = null;

        /**
         * The text of the link when the content is hidden.
         *
         * @type {String}
         */
        this.linkShowText = '';

        /**
         * The text of the link when the content is shown.
         *
         * @type {String}
         */
        this.linkHideText = '';

        for (key in config) {
            if (this.hasOwnProperty(key)) {
                this[key] = config[key];
            }
        }

        if (this.element) {
            link = this.link ? this.link : this.element.select('a[data-dense-text="link"]')[0];
            content = this.content ? this.content : this.element.select('span[data-dense-text="content"]')[0];
            seperator = this.seperator ?
                this.seperator : this.element.select('span[data-dense-text="seperator"]')[0];

            if (link && content && seperator) {
                this.link = link;
                this.content = content;
                this.seperator = seperator;

                linkTxt = this.link[txtProp];

                if (linkTxt !== '') {
                    this.linkShowText = linkTxt;
                }

                if (this.linkHideText === '') {
                    this.linkHideText = 'Show less';
                }

                this.link.observe('click', this.clickEvent.bind(this));

                this.toggle('hide');
            }
        }
    },

    /**
     * An event delegator for the click event.
     *
     * @param  {Event} e
     */
    clickEvent: function(e) {
        if (this.hidden) {
            this.toggle('show');
        }
        else if (!this.hidden) {
            this.toggle('hide');
        }
    },

    /**
     * Toggle between hiding and showing state of the DenseText object.
     *
     * @param  {String} state
     */
    toggle: function(state) {
        if (state === 'show') {
            this.setLinkText('hide');
            this.showSeperator(false);
            this.showContent(true);
            this.setHidden(false);

            if (!this.hideable) {
                this.removeLink();
            }
        }
        else if (state === 'hide') {
            this.setLinkText('show');
            this.showSeperator(true);
            this.showContent(false);
            this.setHidden(true);
        }
    },

    /**
     * Sets the text of the link depending on if the content text is showing or hiding.
     *
     * @param {String} state
     */
    setLinkText: function(state) {
        var txtProp = (document.body.textContent) ? 'textContent' : 'textProperty';

        if (this.link) {
            if (state === 'show') {
                this.link[txtProp] = this.linkShowText;
            }
            else if (state === 'hide' && this.hideable) {
                this.link[txtProp] = this.linkHideText;
            }
        }
    },

    /**
     * Shows or hides the content.
     *
     * @type {Boolean} show
     */
    showContent: function(show) {
        if (this.content) {
            if (show) {
                this.content.style.display = '';
            }
            else if (!show) {
                this.content.style.display = 'none';
            }
        }
    },

    /**
     * Shows or hides the seperator text.
     *
     * @type {Boolean} show
     */
    showSeperator: function(show) {
        if (this.seperator) {
            if (show) {
                this.seperator.style.display = '';
            }
            else if (!show) {
                this.seperator.style.display = 'none';
            }
        }
    },

    /**
     * Sets the state at which the text of the DenseText object is in.
     *
     * @type {Boolean} state
     */
    setHidden: function(state) {
        if (typeof state === 'boolean') {
            this.hidden = state;
        }
    },

    /**
     * Removes the link from the DOM.
     */
    removeLink: function() {
        if (this.link.parentNode) {
            this.link.remove();
        }
    }
});

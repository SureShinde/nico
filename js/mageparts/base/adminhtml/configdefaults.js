/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

/**
 * Creates config default value boxes for input values.
 */
MageParts.Base.Configdefaults = Class.create(MageParts.Base, {

    /**
     * Field list, for more information please see MageParts_Base_Block_Adminhtml_Configdefaults :: getFieldList()
     *
     * @var object
     */
    field_list: {},

    /**
     * Field namespace, for more information please see MageParts_Base_Block_Adminhtml_Configdefaults :: $_fieldNamespace
     *
     * @var string
     */
    field_namespace: '',

    /**
     * Active field list (meaning those fields which are currently using default configuration values). For more
     * information please see field_list above as this setting is based on that.
     *
     * @var object
     */
    active_field_list: {},

    /**
     * Checkbox label.
     *
     * @var config
     */
    checkbox_label: 'Use Config Settings',

    /**
     * Checkbox namespace, please see MageParts_Base_Block_Adminhtml_Configdefaults :: FIELD_USE_CONFIG_SETTINGS_NAMESPACE
     * for more information.
     *
     * @var string
     */
    checkbox_namespace: 'mp_use_config',

    /**
     * Constructor.
     *
     * The keys in the config argument should correspond with the variables described in this object, whether private or
     * shared. It should be noted that variable names starting with an underscore will be ignored.
     *
     * @param obj config
     */
    initialize: function(config)
    {
        // apply submitted configuration
        config = config || {};

        for (var key in this) {
            if (key in config) {
                if (key.indexOf('_') !== 0) {
                    if (typeof config[key] !== "undefined") {
                        this[key] = config[key];
                    }
                }
            }
        }

        this.createConfigCheckboxes();
    },

    /**
     * Create checkbox elements.
     *
     * @return this
     */
    createConfigCheckboxes: function()
    {
        var html, field, fieldName, fieldId, fieldIsActive;
        var that = this;

        //var destinationEl = $('ddq').down('tbody');

        //if (destinationEl && destinationEl.nodeType) {
            for (var i=0; i<this.field_list.length; i++) {
                fieldName = this.field_list[i];
                field = $$('[for=' + fieldName + ']');

                field.each(function(el) {
                    fieldId = (that.field_namespace ? that.field_namespace + '_' + fieldName : fieldName);
                    fieldIsActive = that.inArray(fieldName, that.active_field_list);

                    html = '<div style="display:block; margin:3px 0px 5px 0px;">';
                    html+= '<input type="checkbox" id="' + fieldId+ '" name="' + (that.field_namespace ? that.checkbox_namespace + '[' + fieldName + ']' : fieldName) + '" value="1"' + (fieldIsActive ? ' checked="checked"' : '') + ' onclick="toggleValueElements(this, this.parentNode.parentNode);" class="checkbox">';
                    html+= '<label for="' + fieldId + '_use_config" class="normal" style="position:relative; top:2px;"> ' + that.checkbox_label + '</label>';
                    html+= '</div>';

                    el.up('tr').down('td.value').insert({bottom: html});

                    if (fieldIsActive) {
                        toggleValueElements($(fieldId), $(fieldId).parentNode.parentNode);
                    }
                });

                //console.log(field);
                /*
                var targetElId = targetEls[i];
                var targetEl = $(targetElId).up('tr');

                if (this.storeId === 0 && !this.isOnUpdateAttributePage) {
                    var useConfig = this.useConfig[targetElId];

                    var useConfigHtml = '<input type="checkbox" name="ddq_use_config[' + targetElId + ']" id="' + targetElId + '_use_config" ' + (useConfig ? 'checked="checked" ' : '') + 'onclick="toggleValueElements(this, this.parentNode);" class="checkbox" />';
                    useConfigHtml+= '<label for="' + targetElId + '_use_config" class="normal">Use Config Settings</label>';

                    if (targetElId !== 'ddq_qty_list') {
                        var valueTd = targetEl.down('td.value');
                        valueTd.insert({bottom: useConfigHtml});
                    } else {
                        $('ddq_qty_list').up('td').insert({bottom: useConfigHtml});
                    }

                    if (useConfig) {
                        if (targetElId !== 'ddq_qty_list') {
                            $(targetElId).disabled = true;
                        }
                    }
                }

                if (targetEl) {
                    destinationEl.insert({bottom: targetEl});
                }*/
            }
        //}
    }

});

/*
 * jQuery form plugin
 * @requires jQuery v1.0.2
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 */

jQuery.fn.formToArray = function(semantic) {
    var a = [];
    var q = semantic ? ':input' : 'input,textarea,select,button';

    jQuery(q, this).each(function() {
        var n = this.name;
        var t = this.type;
        var tag = this.tagName.toLowerCase();

        if ( !n || this.disabled || t == 'reset' ||
            (t == 'checkbox' || t == 'radio') && !this.checked ||
            (t == 'submit' || t == 'image' || t == 'button') && this.form && this.form.clk != this ||
            tag == 'select' && this.selectedIndex == -1)
            return;

        if (t == 'image' && this.form.clk_x != undefined)
            return a.push(
                {name: n+'_x', value: this.form.clk_x},
                {name: n+'_y', value: this.form.clk_y}
            );

        if (tag == 'select') {
            // pass select element off to fieldValue to reuse the IE logic
            var val = jQuery.fieldValue(this, false); // pass false to optimize fieldValue
            if (t == 'select-multiple') {
                for (var i=0; i < val.length; i++)
                    a.push({name: n, value: val[i]});
            }
            else
                a.push({name: n, value: val});
        }
        else
            a.push({name: n, value: this.value});
    });
    return a;
};


jQuery.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return jQuery.param(this.formToArray(semantic));
};


jQuery.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        if (!this.name) return;
        var val = jQuery.fieldValue(this, successful);
        if (val && val.constructor == Array) {
            for (var i=0; i < val.length; i++)
                a.push({name: this.name, value: val[i]});
        }
        else if (val !== null && typeof val != 'undefined')
            a.push({name: this.name, value: val});
    });
    //hand off to jQuery.param for proper encoding
    return jQuery.param(a);
};


jQuery.fn.fieldValue = function(successful) {
    var cbVal = [], cbName = null;

    // loop until we find a value
    for (var i = 0; i < this.length; i++) {
        var el = this[i];
        if (el.type == 'checkbox') {
            if (!cbName) cbName = el.name || 'unnamed';
            if (cbName != el.name) // return if we hit a checkbox with a different name
                return cbVal;
            var val = jQuery.fieldValue(el, successful);
            if (val !== null && typeof val != 'undefined') 
                cbVal.push(val);
        }
        else {
            var val = jQuery.fieldValue(el, successful);
            if (val !== null && typeof val != 'undefined') 
                return val;
        }
    }
    return cbVal;
};


jQuery.fieldValue = function(el, successful) {
    var n = el.name;
    var t = el.type;
    var tag = el.tagName.toLowerCase();
    if (typeof successful == 'undefined') successful = true;

    if (successful && ( !n || el.disabled || t == 'reset' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image' || t == 'button') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1))
            return null;
    
    if (tag == 'select') {
        var a = [];
        for(var i=0; i < el.options.length; i++) {
            var op = el.options[i];
            if (op.selected) {
                // extra pain for IE...
                var v = jQuery.browser.msie && !(op.attributes['value'].specified) ? op.text : op.value;
                if (t == 'select-one')
                    return v;
                a.push(v);
            }
        }
        return a;
    }
    return el.value;
};

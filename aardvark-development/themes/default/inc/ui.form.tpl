<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqValidate.js"></script>

{literal}
<script type="text/javascript">
/**
  * Form Javascript Copyright 2008 by Brice Burgess <bhb@iceburg.net>, all rights reserved.
  */
poMMo.form = {
	currentForm: false, // the serial of the current submitted form
	serial: 0,
	hash: [],
	init: function(e,p) {
		e = $(e);
		if(e.size() < 1) { alert('bad form passed to init'); return; }
	
		p = $.extend({
			type: 'ajax',  		// type can be 'ajax' or 'json'. Ajax type forms load their response into the DOM ("target"). JSON type forms evaluate/parse the response.
			onValid: null,		// (for JSON) executed if the form is determined 'valid' [success=false]
			onInvalid: null, 	// (for JSON) executed if the form is determined 'invalid' [success=false]
			target: null,
			beforeSubmit: poMMo.form.defaults.beforeSubmit,
			success: this.defaults.success
		},p);
		
		return e.each(function(){
		
			var s = (this.pfSerial) ? this.pfSerial : poMMo.form.serial++;
			this.pfSerial = s;
			
			p.form = this;
			
			poMMo.form.hash[s] = p;
			
			$(this).ajaxForm({
				dataType: (p.type == 'ajax') ? null : 'json',
				target: (p.type == 'ajax' && p.target == null) ? $(this).parent() : p.target, // load the form response into the parent div if not specified
				beforeSubmit: function(formData,form,params) {
					var s = $(form)[0].pfSerial;
					var hash = poMMo.form.hash[s];
					if(poMMo.form.currentForm !== false) { 
						alert ('cannot submit form at this time [waiting for the return of another]');
						return false;
					}
					poMMo.form.currentForm = s;
					if($.isFunction(hash.beforeSubmit))
						return hash.beforeSubmit(formData,form,params);
				},
				success: function(response) {
					var s = poMMo.form.currentForm;
					poMMo.form.currentForm = false;
					var hash = poMMo.form.hash[s];
					if($.isFunction(hash.success))
						return hash.success(response, hash);
				}
			});
		});
	},
	defaults: {
		// Default beforeSubmit callback [if not overriden]
		beforeSubmit: function(formData,form,params) {	
			// reset errors
			$('label span.error',form).remove();
			$('div.output',form).html('');
			
			// toggle submit/loading state
			$('input[@type=submit],img[@name=loading]', form).toggle();
		},
		// Default success callback [if not overriden]
		success: function(response, hash) { 
			
			// if we're expecting a JSON return, execute the default JSON callback
			if(hash.type == 'json')
				return poMMo.form.defaults.jsonSuccess(response, hash);
			
			// reassign the form [designed to work in default setting, on forms with class ajax]
			var form = $('form.ajax',hash.target)[0] || false;
			if(form) {
				form.pfSerial = hash.form.pfSerial; // conserve memory!
				poMMo.form.init(form,hash);
			}
		},
		jsonSuccess: function(json, hash) {
			// execute a callback function if passed (and exists).
			//   If the callbackParams exist, pass them to the callbackFunction. If not, pass the JSON return
			//   If the callback returns false, halt execution.
			if(json.callbackFunction && $.isFunction(poMMo.callback[json.callbackFunction])) {
				json.callbackParams = json.callbackParams || json;
				if(!poMMo.callback[json.callbackFunction](json.callbackParams))
					return false;
			}
			
			// toggle submit/loading state
			$('input[@type=submit],img[@name=loading]', hash.form).toggle();
			
			// check for and execute onValid/onInvalid callbacks
			if(json.success && $.isFunction(hash.onValid))
				return hash.onValid(json,hash);
			else if(!json.success && $.isFunction(hash.onInvalid))
				return hash.onInvalid(json,hash);
		 	
		 	// output any message(s) or errors(s)
		 	if(json.messages.length > 0)
				$('div.output',hash.form).html(poMMo.implode(json.messages));
			if(json.errors.length > 0)
				$('div.output',hash.form).append('<div class="error">'+poMMo.implode(json.errors)+'</div>');
		
			// append error messages to form fields
			for (var i=0;i<json.fieldErrors.length;i++) 
					$('label[@for='+json.fieldErrors[i].field+']',hash.form).append('<span class="error">'+json.fieldErrors[i].message+'</span>');
		}
	}
};
</script>
{/literal}
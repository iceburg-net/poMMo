<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>

{literal}
<script type="text/javascript">
poMMo.form = {
	currentForm: null,
	onSuccess: null,
	onFail: null,
	serial: 0,
	hash: [],
	default: {
		jsonSuccess: function(json,scope){
			if(json.message){
				$('div.output',scope).html(json.message);
			}
			return true;
		},
		jsonFail: function(json,scope){
			
			if(json.message){
				$('div.output',scope).html(json.message);
			}
			
			if(json.errors) {
				// append error messages to form fields
				for (var i=0;i<json.errors.length;i++)
					$('label[@for='+json.errors[i].field+']',scope).append('<span class="error">'+json.errors[i].message+'</span>');
			}
			return false;
		}
	},
	prepJSON: function(e,success,fail) {
		e = $(e);
		if(e.size() < 1) { alert('bad form passed to prepJSON'); return; }
		
		var s = e[0].pommoForm || this.serial++;
		e[0].pommoForm = s;
		
		this.hash[s] = {
			onSuccess: ($.isFunction(success)) ? success : this.default.jsonSuccess,
			onFail: ($.isFunction(fail)) ? fail : this.default.jsonFail
		};

		return e.ajaxForm({
			dataType: 'json',
			beforeSubmit: function(formData,form,params) {
				// reset errors
				$('label span.error',form).remove();
				$('div.output',form).html('');
				
				// toggle submit/loading state
				$('input[@type=submit],img[@name=loading]', form).toggle();
			
				poMMo.form.currentForm = $(form)[0];
			},
			success: function(json) {
				if(json.callbackFunction && $.isFunction(poMMo.callback[json.callbackFunction])) { // callbacks can be defined in the JSON return
					poMMo.callback[json.callbackFunction](json.callbackParams);
				}
				
				var form = poMMo.form.currentForm;
				var hash = poMMo.form.hash[form.pommoForm];
				
				// toggle submit/loading state
				$('input[@type=submit],img[@name=loading]', form).toggle();
				
				if(json.success) 
					return hash.onSuccess(json,form);
				return hash.onFail(json,form);
			}
		});
	},
	prepAJAX: function(e,p) { // submits a form via ajax and loads the response into target
		e = $(e);
		if(e.size() < 1) { alert('bad form passed to prepAJAX'); return; }
		
		var defaults = {
			target: e
		};
		
		return e.ajaxForm($.extend(defaults,p));
	}
};
</script>
{/literal}
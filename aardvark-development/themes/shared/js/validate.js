// quick form validation (c) Brice Burgess <bhb@iceburg.net> 2006
// matched inputs with the following classes will be checked;
//   pvDate (date), pvNumber (number), pvEmpty (not blank)
var PommoValidate = {
	// input = form input selector
	// submit = form submit button selector
	// warn = alert errors/warnings (bool)
	// scope = DOM scope limiter of input/submit lookups
	ranInit: false,
	init: function(inputs, submit, warn, scope) {
		if (this.ranInit)
			return;
		this.ranInit = true;
		var warn = (typeof(warn) != 'undefined') ? warn : true;
		var scope = (typeof(scope) != 'undefined') ? scope : false;
		
		this.submit = (scope) ? $(submit, scope) : $(submit);
			if (this.submit.size() != 1) {
				this.submit = false;
				if(warn) alert('Submit selector did not return 1 DOM element');
			}
		this.inputs = (scope) ? $(inputs, scope) : $(inputs);
			if (this.inputs.size() < 1) {
				this.inputs = false;
				if(warn) alert('Input selector did not return any DOM elements');
			}
			else {
				// assign events
				this.inputs.mouseup(function() { PommoValidate.validate(this); });
				this.inputs.keyup(function() { PommoValidate.validate(this); });
			}

		this.validate();
	},
	validate: function(e) {
		if (!this.inputs)
			return;
		
		var e = (typeof(e) != 'undefined') ? $(e) : this.inputs;
		e.each(function(){
			var a = new Array();
			if ($(this).is('.pvNumber')) a.push('number');
			if ($(this).is('.pvDate')) a.push('date');
			if ($(this).is('.pvEmpty')) a.push('empty');
			if ($(this).is('.pvEmail')) a.push('email');
			
			valid = true;
			value = $(this).val();
			
			for (var i = 0; i < a.length; i++) {
				if (!PommoValidate.checkInput(value, a[i]))
					valid = false;
			}
			
			(valid) ?
				$(this).removeClass('pvInvalid') :
				$(this).addClass('pvInvalid');
		});
		
		(this.inputs.is('.pvInvalid')) ?
			this.disable() :
			this.enable();
		
	},
	checkInput: function(value, type) {
		switch(type) {
			case 'number' :
				var regex = /^\d+$/;
				return (regex.test(value));
				break;
			case 'date' :
				var regex = /^\d\d?\/\d\d?\/\d{4}$/;
				return (regex.test(value));
				break;
			case 'empty' :
				value.replace(/^\s*|\s*$/g,"");
				return (value == '') ? false : true;
				break;
			case 'email' :
				var regex = /^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/;
				return (regex.test(value));
				break;
		}
	},
	disable: function() {
		if (!this.submit)
			return;
		
		this.submit.bind("click",function() { return false; });
		var e = this.submit.get(0);
		e.disabled = true;
		this.submit.fadeTo(1,0.5);
	},
	enable: function() {
		if (!this.submit)
			return;
		this.submit.unbind("click",function() { return false; });
		var e = this.submit.get(0);
		e.disabled = false;
		this.submit.fadeTo(1,1);
	},
	reset: function() {
		this.submit = false;
		this.inputs = false;
		this.warn = false;
		this.ranInit = false;
	}
};
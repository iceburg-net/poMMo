// poMMo javascript library (c) Brice Burgess

if (typeof(poMMo) == 'undefined') {
	var poMMo = {
		callback: { // RPC/Ajax Callback Functions
			redirect: function(url) {
				this.pause();
				window.location = url;
				return false;
			},
			pause: function() { this.pause(); },
			resume: function() { this.resume(); }
		}, 
		confirmMsg: 'Are you sure?',
		confirm: function(message){
			message = message || this.confirmMsg;
			return confirm(this.confirmMsg);
		},
		isSet: function(arg){
			return (typeof(args.success) != 'undefined');
		},
		implode: function(msg, seperator) {
			seperator = seperator || '<br />';
			if(!msg instanceof Array)
				msg = new Array(msg);
			return msg.join(seperator);
		},
		pause: function() {
			$('#wait').jqmShow();
		},
		resume: function() {
			$('#wait').jqmHide();
		}
	};
}

// poMMo javascript library (c) Brice Burgess

if (typeof(poMMo) == 'undefined') {
	var poMMo = {
		callback: {}, // RPC/Ajax Callback Functions
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
		}
	};
}

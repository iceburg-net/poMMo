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
		}
	};
}

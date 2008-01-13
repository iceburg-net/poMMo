// poMMo javascript library (c) Brice Burgess

var poMMo = {
	confirmMsg: 'Are you sure?',
	confirm: function(message){
		message = message || this.confirmMsg;
		return confirm(this.confirmMsg);
	},
	callback: {} // RPC/Ajax Callback Functions
};

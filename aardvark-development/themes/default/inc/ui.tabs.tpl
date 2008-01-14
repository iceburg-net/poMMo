<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.tabs.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/ui.tabs.css" />


<script type="text/javascript">
/* TabWizzard JS (c) 2007 Brice Burgess, <bhb@iceburg.net>
	Licensed under the GPL */
	
	// forms with class "mandatory" are force submitted and verified before changing tabs.
	
var PommoTabs = {ldelim}
	tabs: null,
	clicked: false,
	mandatoryForm: false,
	force: false,
	defaults: {ldelim}
		spinner: "{t}Processing{/t}...",
		{literal}
		ajaxOptions: { async: false }, // make synchronous requests when loading tabs
		click: function(clicked,hide,show) { return PommoTabs.click(clicked,hide,show); },
		load: function(clicked,content) { return PommoTabs.load(content); }
	},
	init: function(e,p) {
		this.tabs = $(e).tabs($.extend(this.defaults,p));
		return this;
	},
	load: function(tab) {
		this.clicked = false;
		this.mandatoryForm = false;
		$('form.json',tab).each(function(){
			if($(this).hasClass('mandatory'))
				this.mandatoryForm = poMMo.form.prepJSON(this,PommoTabs.switch);
			else
				poMMo.form.prepJSON(this);
		});
	},
	click: function(tab) {
		this.clicked = tab;
		if(this.mandatoryForm && !this.force) {
			this.mandatoryForm.submit(); // onSuccess fires PommoTabs.switch();
			return false;
		}
		this.force = false;
		return true;
	},
	switch: function() {
		this.force = true;
		if(!this.clicked)
			this.clicked = $('li a',this.tabs)[this.tabs.tabsSelected()];
		
		$(this.clicked).click();
	}
}
</script>
{/literal}
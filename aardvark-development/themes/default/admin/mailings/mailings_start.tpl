{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.tabs.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>

{* Include the WYSIWYG Javascripts *}
{foreach from=$wysiwygJS item=js}
	<script type="text/javascript" src="{$url.theme.shared}../wysiwyg/{$js}"></script>
{/foreach}

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/ui.tabs.css" />
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Mailings Page{/t}</a></li>
</ul>

{include file="inc/messages.tpl"}

<hr />
        
<div id="tabs">
	<ul>
	    <li><a href="mailing/setup.php" title="{t}Setup{/t}"><span>{t}Setup{/t}</span></a></li>
	    <li><a href="mailing/templates.php" title="{t}Templates{/t}"><span>{t}Templates{/t}</span></a></li>
	    <li><a href="mailing/compose.php" title="{t}Compose{/t}"><span>{t}Compose{/t}</span></a></li>
	    <li><a href="mailing/preview.php" title="{t}Preview{/t}"><span>{t}Preview{/t}</span></a></li>
	</ul>
</div>

{literal}
<script type="text/javascript">
/* TabWizzard JS (c) 2007 Brice Burgess, <bhb@iceburg.net>
	Licensed under the GPL */
var poMMo = {
	// tabClick: Called when a tab is clicked.
	tabClick: function(clicked, stop) {
		this.tabClicked = clicked;
		if(this.tabForm && !this.forceLoad)
			return(!this.tabForm.submit()); // stops loading of tab, submits ajaxForm
		if(stop) return false;
		this.forceLoad = false
	},
	// tabLoaded: Called after a tab is loaded.
	tabLoaded: function(tab) {
		this.tabClicked = false;
		var form = $('form.ajax',tab)[0];
		this.tabForm = (form) ? 
			this.formJSON(form) : 
			false;	
	},
	// tabSwitch: Called to switch a tab
	tabSwitch: function() {
		this.forceLoad = true;
		if(!this.tabClicked)
			this.tabClicked = $('li a',this.tabs)[this.tabs.tabsSelected()];
		
		$(this.tabClicked).click();
	},
	tabClicked: false, // The clicked tab, or false 
	tabForm: false, // The form element in the tab, or false if not exists
	tabs: null,
	forceLoad: false,
	formJSON: function(form) {
		return $(form).ajaxForm({
			dataType: 'json',
			beforeSubmit: function(data,form,options) {
				// reset errors
				$('label span.error',form).remove();
				
				// toggle submit/loading state
				$('input[@type=submit],img[@name=loading]', form).toggle();
			},
			success: function(json) {
				if(json.callback)
					if(!poMMo.formCallback(json.callback))
						return; // if callback function returns false, stop.
				
				if(json.message){
					$('div.output').html(json.message);
				}
				
				if(json.errors) {
					// append error messages to form fields
					for (var i=0;i<json.errors.length;i++)
						$('label[@for='+json.errors[i].field+']').append('<span class="error">'+json.errors[i].message+'</span>');
				}
				
				if(json.success) 
					return poMMo.tabSwitch();
				
				// toggle submit/loading state
				$('input[@type=submit],img[@name=loading]', form).toggle();
			}
		});
	},
	formCallback: false
}

$().ready(function(){ 
	
	poMMo.tabs = $('#tabs').tabs({
		spinner: '{/literal}{t escape=js}loading...{/t}{literal}',
		ajaxOptions: { async: false }, // make synchronous requests when loading tabs
		click: function(clicked,hide,show) { return poMMo.tabClick(clicked); },
		load: function(clicked,content) { poMMo.tabLoaded(content); }
	});
	
	
	// initialize all dialogs
	
	$('#wait, #addTemplate, #testMailing, #personalize').jqm({
		trigger: false,
		target: 'div.jqmdMSG',
		ajaxLoadText: '{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t escape=js}Please Wait{/t}...{literal}'
	}).jqDrag('div.jqmdTC');
	
	// tailor the dialogs
	
	$('#wait').jqm({modal: true, overlay: 0});
	$('#addTemplate').jqm({ajax: 'mailing/ajax.addtemplate.php'});
	$('#testMailing').jqm({ajax: 'mailing/ajax.mailingtest.php'});
	$('#personalize').jqm({ajax: 'mailing/ajax.personalize.php'});
	
});

// initialize wysiwyg namespace


/*

var lang="{/literal}{$lang}{literal}";
var s=',separator,';


// poMMo languages not supported by TinyMCE:
switch (lang) {
	case 'pt':
	case 'pt-br':
		lang='pt_br';
		break;
	case 'bg':
	case 'en-uk':
		lang='en';
		break;
}


tinyMCE.init({
	mode : 'none',
	theme : 'advanced',
	plugins : 'style',
	language: lang,
	entity_encoding: 'raw',
	theme_advanced_buttons1 : 
		'bold,italic,underline,strikethrough'+s+
		'bullist,numlist'+s+
		'link,unlike,image'+s+
		'hr,sub,sup,charmap'+s+
		'forecolor,backcolor,styleprops'+s+
		'undo,redo'
		,
	theme_advanced_buttons2 : 
		'justifyleft,justifycenter,justifyright,justifyfull'+s+
		'outdent,indent'+s+
		'formatselect,fontselect,fontsizeselect'+s+
		'removeformat'
		,
	theme_advanced_buttons3 : "",
	extended_valid_elements : "style[dir<ltr?rtl|lang|media|title|type]", // can add more entities, see tinymce page!
	remove_linebreaks : false,
	convert_urls : false
});

*/

</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" dialogID="wait" dialogNoClose=true dialogBodyClass="jqmdShort"}
{include file="inc/dialog.tpl" dialogID="personalize" dialogTitle=$t_personalize dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="addTemplate" dialogTitle=$t_saveTemplate dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="testMailing" dialogTitle=$t_testMailing dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{/capture}

{include file="inc/admin.footer.tpl"}
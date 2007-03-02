{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jq11.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/history.js" ></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/tabs.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/tabs.css" />
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Mailings Page{/t}</a></li>
</ul>

{include file="inc/messages.tpl"}

<hr />

<div id="mailing">
	<ul class="anchors">
	    <li><a href="mailing/setup.php">{t}Setup{/t}</a></li>
	    <li><a href="mailing/templates.php">{t}Templates{/t}</a></li>
	    <li><a href="mailing/compose.php">{t}Compose{/t}</a></li>
	    <li><a href="mailing/preview.php">{t}Preview{/t}</a></li>
	</ul>
</div>

{literal}
<script type="text/javascript">

// globals
clickedTab = false; // tab to open

$().ready(function(){ 
	$('#mailing').tabs({
		remote: true,
		onClick: function(tab, loading, current){
			$('form',current).submit();
			clickedTab = tab;
			return false; // prevent tab from activating
			}, 
		onShow: function(tab, loading, current){
			assignForm(loading);
			clickedTab = false;
			},
		onHide: function(){ return;
			}});


});

function assignForm(scope) {
	$('form',scope).ajaxForm( { 
		target: scope,
		beforeSubmit: function() {
			$('input[@type=submit]', scope).hide();
			$('img[@name=loading]', scope).show();
		},
		success: function() { 
			assignForm(this); 
			$('div.output',this).fadeTo(5000,0.35);
			
			if($('#success')[0] && clickedTab)
				$(clickedTab).trigger('triggerTab'); // load clicked tab
			else
				$('#mailing').triggerTab($('#success').val()); // load "next" tab
			}
		}
	);
}
</script>
{/literal}

{include file="inc/admin.footer.tpl"}
{include file="admin/inc.header.tpl"}

<h2>{t}Mailings History{/t}</h2>

{include file="admin/inc.messages.tpl"}

<!-- Ordering options -->
<form method="post" action="" name="bForm" id="bForm">
<fieldset class="sorting">
<legend>Sorting</legend>

<div>
<label for="limit">{t}Mailings per Page:{/t}</label>
<select name="limit" id="limit" onChange="document.bForm.submit()">
<option value="10"{if $state.limit == '10'} selected="selected"{/if}>10</option>
<option value="20"{if $state.limit == '20'} selected="selected"{/if}>20</option>
<option value="50"{if $state.limit == '50'} selected="selected"{/if}>50</option>
<option value="100"{if $state.limit == '100'} selected="selected"{/if}>100</option>
</select>
</div>

<div>
<label for="sortBy">{t}Order by:{/t}</label>
<select name="sortBy" id="sortBy" onChange="document.bForm.submit()">
<option value="subject"{if $state.sortBy == 'subject'} selected="selected"{/if}>subject</option>
<option value="started"{if $state.sortBy == 'started'} selected="selected"{/if}>Start Date</option>
<option value="finished"{if $state.sortBy == 'finished'} selected="selected"{/if}>Finish Date</option>
<option value="mailgroup"{if $state.sortBy == 'mailgroup'} selected="selected"{/if}>Mail group</option>
<option value="sent"{if $state.sortBy == 'sent'} selected="selected"{/if}>Mails Sent</option>
<option value="ishtml"{if $state.sortBy == 'ishtml'} selected="selected"{/if}>HTML Mail</option>
</select>
<select name="sortOrder" onChange="document.bForm.submit()">
<option value="ASC"{if $state.sortOrder == 'ASC'} selected="selected"{/if}>{t}ascending{/t}</option>
<option value="DESC"{if $state.sortOrder == 'DESC'} selected="selected"{/if}>{t}descending{/t}</option>
</select>
</div>

</fieldset>
</form>

<form method="post" action="mailings_mod.php" name="oForm" id="oForm">
<fieldset>
<legend>Subscribers</legend>

<p class="count">(<em>{t 1=$rowsinset}%1 mailings{/t}</em>)</p>

<table id="mailingtable" summary="history of sent email">
<thead>
<tr>
<th>{t}select{/t}</th>
<th>{t}delete{/t}</th>
<th>{t}view{/t}</th>
<th>{t}reload{/t}</th>
<th>{t}Subject{/t}</th>
<th>{t}Group (count){/t}</th>
<th>{t}Sent{/t}</th>
<th>{t}Started{/t}</th>
<th>{t}Finished{/t}</th>
<th>{t}Duration{/t}</th>
<th>{t}HTML{/t}</th>
</tr>
</thead>

<tbody>

{foreach name=mailloop from=$mailings key=key item=mailitem}

<tr class="{cycle values="alt1,alt2"}">
<td nowrap><input type="checkbox" name="mailid[]" value="{$mailitem.mailid}" /></td>
<td nowrap><a href="mailings_mod.php?mailid={$mailitem.mailid}&amp;action=delete">{t}delete{/t}</a></td>
<td nowrap><a href="mailings_mod.php?mailid={$mailitem.mailid}&amp;action=view">{t}view{/t}</a></td>
<td nowrap><a href="mailings_mod.php?mailid={$mailitem.mailid}&amp;action=reload"><img src="{$url.theme.shared}images/icons/reload-small.png" alt="reload icon" title="{t}Reload, edit and resend Mail{/t}" /></a></td>
<td nowrap><i>{$mailitem.subject}</i></td>
<td nowrap>{$mailitem.mailgroup} <span>({$mailitem.subscriberCount})</span></td>
<td nowrap>{$mailitem.sent}</td>
<td nowrap>{$mailitem.started}</td>
<td nowrap>{$mailitem.finished}</td>
<td nowrap>{$mailitem.duration} 

{if $mailitem.mps}
<span>({$mailitem.mps} {t}msgs/sec{/t})</span></td>
{/if}

<td>
{if $mailitem.ishtml == 'on'}
<a href="mailing_preview.php?action=viewhtml&amp;viewid={$mailitem.mailid}" target="_blank"><img src="{$url.theme.shared}images/icons/viewhtml.png" alt="view html icon" title="{t}View HTML in new browser window{/t}" /></a>
{/if}
</td>

{*{foreach name=propsloop from=$mailitem key=key item=item}
<td nowrap>{$item}</td> {$key}:{$item}
{/foreach}-$mailitem.finished}*}

</tr>				

{foreachelse}
<tr>
<td colspan="11">{t}No mailing found{/t}</td>
</tr>
{/foreach}

</tbody>
</table>

</fieldset>

<fieldset class="controls">
<legend>{t}Controls{/t}</legend>

<ul>
<li><a href="javascript:SetChecked(1,'mailid[]');">{t}Check All{/t}</a></li>
<li><a href="javascript:SetChecked(0,'mailid[]');">{t}Clear All{/t}</a></li>
</ul>

<select name="action">
<option value="view">{t}View{/t} {t}checked mailings{/t}</option>
<option value="delete">{t}Delete{/t} {t}checked mailings{/t}</option>
</select>

</fieldset>

<div class="buttons">

<input type="submit" name="send" value="{t}go{/t}" />

</div>

{$pagelist}

</form>

{literal}
<script type="text/javascript">
// <![CDATA[

/* The following code is to "check all/check none" NOTE: form name must properly be set */
var form='oForm' //Give the form name here
function SetChecked(val,chkName) {
	dml=document.forms[form];
	len = dml.elements.length;
	var i=0;
	for( i=0 ; i<len ; i++) {
		if (dml.elements[i].name==chkName) {
			dml.elements[i].checked=val;
		}
	}
}
// ]]>
</script>
{/literal}

{include file="admin/inc.footer.tpl"}
{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/effects.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/dragdrop.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/controls.js" type="text/javascript"></script>
{/capture}
{include file="admin/inc.header.tpl"}

<h2>{t}Fields Page{/t}</h2>

{if $intro}<p><img src="{$url.theme.shared}images/icons/fields.png" alt="fields icon" class="articleimg" /> {$intro}</p>{/if}

<form method="post" action="">

{include file="admin/inc.messages.tpl"}
 
<fieldset>
<legend>{t}Fields{/t}</legend>

<div>
<label for="field_name">New field name:</label>
<input type="text" class="text" title="{t}type new field name{/t}" maxlength="60" size="30" name="field_name" id="field_name" />
</div>

<div>
<label for="field_type">Value type:</label>
<select name="field_type" id="field_type">
<option value="text">{t}Text{/t}</option>
<option value="number">{t}Number{/t}</option>
<option value="checkbox">{t}Checkbox{/t}</option>
<option value="multiple">{t}Multiple Choice{/t}</option>
<option value="date">{t}Date{/t}</option>
</select>
</div>

<div class="buttons">

<input type="submit" value="{t}Add{/t}" />

</div>

</fieldset>
</form>

<div class="reorder">

<h3>Field order</h3>

<ul>
<li>{t escape=no}Names in <strong>bold</strong> are active.{/t}</li>
<li>{t}Change the ordering of fields on the subscription form by dragging and dropping the order icon{/t}</li>
</ul>

<div>
<span>{t}Delete{/t}</span>
<span>{t}Edit{/t}</span>
<span>{t}Order{/t}</span>
<span class="fieldname">{t}Field Name{/t}</span>
</div>

<div>
<span>------</span>
<span>------</span>
<span>------</span>
<span class="fieldname"><strong>Email</strong></span>
</div>

<div id="demoOrder">

{foreach name=demos from=$fields key=key item=demo}
<div id="demo_{$key}">
<span><a href="{$smarty.server.PHP_SELF}?field_id={$key}&amp;delete=TRUE&amp;field_name={$demo.name}"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></a></span>

<span><a href="fields_edit.php?field_id={$key}"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></a></span>

<span class="handle"><img src="{$url.theme.shared}images/icons/order.png" alt="order icon" /></span>

<span class="fieldname">
{if $demo.active == 'on'}<strong>{$demo.name}</strong>{else}{$demo.name}{/if}
 ({$demo.type})
</span>
</div>	

{foreachelse}
<div><strong>{t}No fields have been assigned.{/t}</strong></div>
{/foreach}

</div>

</div>
<!-- end reorder -->

<div id="ajaxOutput" class="alert"></div>

{literal}
<script type="text/javascript">
// <![CDATA[

Sortable.create('demoOrder',{tag:'div', handle: 'handle', onUpdate:function(){new Ajax.Updater('ajaxOutput', 'ajax_demoOrder.php', {onComplete:function(request){new Effect.Highlight('demoOrder',{});}, parameters:Sortable.serialize('demoOrder'), evalScripts:true, asynchronous:true})}});

// ]]>
</script>
{/literal}

{include file="admin/inc.footer.tpl"}
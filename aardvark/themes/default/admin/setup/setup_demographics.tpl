{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}


<script src="{$url.theme.shared}/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}/scriptaculous/effects.js" type="text/javascript"></script>
<script src="{$url.theme.shared}/scriptaculous/dragdrop.js" type="text/javascript"></script>
<script src="{$url.theme.shared}/scriptaculous/controls.js" type="text/javascript"></script>

{literal}
<style type="text/css">
    div.auto_complete {
      position:absolute;
      width:250px;
      background-color:white;
      border:1px solid #888;
      margin:0px;
      padding:0px;
    }
    ul.contacts  {
      list-style-type: none;
      margin:0px;
      padding:0px;
    }
    ul.contacts li.selected { background-color: #ffb; }
    li.contact {
      list-style-type: none;
      display:block;
      margin:0;
      padding:2px;
      height:32px;
    }
    li.contact div.image {
      float:left;
      width:32px;
      height:32px;
      margin-right:8px;
    }
    li.contact div.name {
      font-weight:bold;
      font-size:12px;
      line-height:1.2em;
    }
    li.contact div.email {
      font-size:10px;
      color:#888;
    }
    #list {
      margin:0;
      margin-top:10px;
      padding:0;
      list-style-type: none;
      width:250px;
    }
    #list li {
      margin:0;
      margin-bottom:4px;
      padding:5px;
      border:1px solid #888;
      cursor:move;
    }
  </style>
 {/literal}


<div id="mainbar">

<h1>{t}Demographics Page{/t}</h1>

<img src="{$url.theme.shared}/images/icons/demographics.png" class="articleimg">

{if $intro}<p>{$intro}</p>{/if}


<h2>{t}Demographics{/t} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 <form action="" method="POST">
	<div class="field">
		<b>{t}Make New{/t} &raquo;</b>
		<input type="text" class="text"  title="{t}type new demographic name{/t}" maxlength="60" size="30" 
		name="demographic_name" id="demographic_name"  value="{t}type new demographic name{/t}" />
		<select name="demographic_type">
			<option value="text">{t}Text{/t}</option>
			<option value="number">{t}Number{/t}</option>
			<option value="checkbox">{t}Check Box{/t}</option>
			<option value="multiple">{t}Multiple Choice{/t}</option>
			<option value="date">{t}Date{/t}</option>
		</select>
		<input class="button" type="submit" value="{t}Add{/t}" />
	</div>
</form>

<div width="100%" id="demoOrder">
<span>{t}Order{/t}</span>
<span style="margin-left: 20px;">{t}Delete{/t}</span>
<span style="margin-left: 20px; margin-right: 20px;">{t}Edit{/t}</span>
<span style="text-align:left; margin-left: 5px;">{t}Demographic Name{/t}</span>

{foreach name=demos from=$demographics key=key item=demo}
<div id="demo_{$key}">
	<span style="cursor:move"><img src="{$url.theme.shared}/images/icons/order.png"></span>
	<span style="margin-left: 20px;">
	<a href="{$smarty.server.PHP_SELF}?demographic_id={$key}&delete=TRUE&demographic_name={$demo.name}">
 	 		<img src="{$url.theme.shared}/images/icons/delete.png" border="0"></a>
	</span>
	<span style="margin-left: 20px; margin-right: 20px;">
	<a href="demographics_edit.php?demographic_id={$key}">
			<img src="{$url.theme.shared}/images/icons/edit.png" border="0"></a>
	</span>
	<span style="text-align:left; margin-left: 5px;">
	{if $demo.active}<b>{$demo.name}</b>{else}{$demo.name}{/if}
			 ({$demo.type})
	</span>
</div>
{foreachelse}
 	<div><br><strong>{t}No demographics have been assigned.{/t}</strong></div>
{/foreach}

</div>
<br>
<div><li>{t escape=no}Names in <strong>bold</strong> are active.{/t}</li></div>
<div><li>{t}Change the ordering of fields on the subscription form by dragging and dropping the order icon{/t}</li></div>
 
<div id="ajaxOutput" class="msgdisplay"></div>
 
   
{literal}
<script type="text/javascript">
// <![CDATA[
Sortable.create('demoOrder',{tag:'div', onUpdate:function(){new Ajax.Updater('ajaxOutput', 'ajax_demoOrder.php', {onComplete:function(request){new Effect.Highlight('demoOrder',{});}, parameters:Sortable.serialize('demoOrder'), evalScripts:true, asynchronous:true})}});
// ]]>
</script>
{/literal}

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}
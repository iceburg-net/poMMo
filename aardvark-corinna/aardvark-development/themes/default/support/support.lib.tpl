{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}
{include file="admin/inc.header.tpl"}

<h2>poMMo support v0.02</h2>

<ul>
<li><a href="tests/file.clearWork.php?height=320&amp;width=480" title="Clear Work Directory" class="thickbox">Clear Work Directory</a></li>
<li><a href="tests/mailing.test.php?height=320&amp;width=480" title="Test Mailing Processor" class="thickbox">Test Mailing Processor</a></li>
<li><a href="tests/mailing.kill.php?height=320&amp;width=480" title="Terminate Current Mailing" class="thickbox">Terminate Current Mailing</a></li>
<li><a href="tests/mailing.runtime.php?height=320&amp;width=480" title="Test Max Runtime" class="thickbox">Test Max Runtime (takes 40 seconds)</a></li>
</ul>

{include file="admin/inc.footer.tpl"}
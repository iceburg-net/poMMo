<script type="text/javascript" src="{$url.theme.shared}js/jq/grid.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/jqgrid.css" />

<script type="text/javascript">
var PommoGrid = {ldelim}
	grid: null,
	defaults: {ldelim}
		loadtext: "{t}Processing{/t}...",
		recordtext: "{if empty($state.search)}{t}Record(s){/t}{else}{t}Match(es){/t}{/if}",
		imgpath: "{$url.theme.shared}/images/grid",
		{literal}
		colNames: [],
		colModel: [],
		rowNum: 10,
		url: 'ajax/404',
		datatype: 'json',
		pager: jQuery('#gridPager'),
		viewrecords: true,
		multiselect: true,
		height: 270,
		width: 670,
		shrinkToFit: false,
		jsonReader: {repeatitems: false}
	},
	init: function(e,p) {
		this.grid = $(e).jqGrid($.extend(this.defaults,p));
		return this;
	},
	getRowID: function() {
		var row = this.grid.getSelectedRow();
		return (row == null) ? false : row;
	},
	getRowIDs: function() {
		var ids = this.grid.getMultiRow();
		return (ids.length == 0) ? false : ids;
	},
	delRow: function(ids) {
		if (!(ids instanceof Array))
			ids = [ids];
		for (i=0; i<ids.length; i++)
			this.grid.delRowData(ids[i]);
	},
	reset: function() {
		// todo; Add method to jqGrid which clears selection.
		return;
	}
}
</script>
{/literal}
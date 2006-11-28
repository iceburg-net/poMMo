/*
 *
 * TableEditor - In place AJAX editing of TableSorter!
 *
 * Copyright (c) 2006 Brice Burgess (http://www.iceburg.net)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Date: 2006-10-24 14:43:23 +0000 
 * $Version: 0.0 (alpha)
 * 
 */
jQuery.fn.tableEditor = function(o) {

	/**
	 * Assign default parameters. 
	 *
	 * EDIT_HTML : HTML/TEXT that EVENT_LINK changes to when converted to a "edit link".
	 *   Default : Uses the link's previous html
	 *
	 * SAVE_HTML : HTML/TEXT that EVENT_LINK changes to when converted to a "save link".
	 *   Default : "Save"
	 *
	 * EVENT_LINK_SELECTOR : // Selector used within a row's table cells to assign the EDIT ROW EVENT. 
	 *   Default: Assign to links with a class of "tsEditLink" (matches: <a class="tsEditLink">Edit</a>)
	 *
	 * ROW_KEY_SELECTOR: Selector used within a row's table cells to get the row key/id. 
	 *  This is used to associate a row with an underlying ID which is especially useful when updating
	 *  a table with data fetched from a database (assigned as the PRIMARY_KEY of a recordset).
	 *   Default: Assign to the text contained between the "key tag" (matches: <key>1202</key>)
	 *
	 * COL_NOEDIT_SELECTOR: Selector used against the table head elements <th>. If matched, this column
	 *   will be ignored and not made into a editable field nor available in the passed object (o.row)
	 * 
	 * COL_APPLYCLASS: (bool) TRUE/FALSE. If true, all classes found in <th> will be inherrited by
	 *   the edit row (<td>) columns when the EDIT_EVENT is fired.
	 * 
	 * ---------------
	 *  NOTE: These can be overriden and passed during runtime via;
	 *  $().ready(function() {	
	 *    $("#editableTable").tableSorter().tableEditor({
	 *      EDIT_HTML: 'EDIT2',
	 *      SAVE_HTML: 'Save'
	 *    });
	 *  }); 
	 * 
	 * ===CALLBACK FUNCTIONS===
	 *   Every callback function is passed an object (o) containing:
	 *    o.row: jQuery object consisting of the row's editable cells
	 *    o.key: the row key (extracted via ROW_KEY_SELECTOR)
	 *    
	 *   The Update callback function is additionally passed the following:
	 *    o.changed: Array representing the changed/updated values of a row (name:value) 
	 *    o.original: The original value of updated cells from o.update (name:value)
	 *      this is used to restore value if the update failed/was rejected by server side validation.
	 *   
	 *   FUNC_PRE_EDIT: Executed before a row's cells are made editable
	 *     Example Use: Switch a regular text cell to a multiple-choice <select> 
	 * 
	 *   FUNC_POST_EDIT: Executed after a row's cells are made editable
	 *     Example Use: Inject client side validation on the newly made input fields
	 * 
	 *   FUNC_PRE_SAVE: Executed before a row's cells are made not editable
	 *     Example Use: Sanitize/Normalize user input
	 * 
	 *   FUNC_UPDATE: Executed after a row's cells are made not editable
	 *     Example Use: Update the datasource through an AJAX call
	 */
	  
	var defaults =  {		
		EDIT_HTML: null,
		SAVE_HTML: "Save",
		EVENT_LINK_SELECTOR: "a.tsEditLink", 
		ROW_KEY_SELECTOR: "key",
		COL_NOEDIT_SELECTOR: ".noEdit",
		COL_APPLYCLASS: false,
		FUNC_PRE_EDIT: false,
		FUNC_POST_EDIT: false,
		FUNC_PRE_SAVE: false,
		FUNC_UPDATE: false,
		
		// (should be) non configurable
		COLUMN_NAMES: new Array(), // holds the name (assigned via <th name="...">) of each column
		COLUMN_NOEDIT: new Array(), // holds the column index of columns to ignore/not edit
		COLUMN_CLASSES: new Array()
	};
	jQuery.extend(defaults, o);

	function editRow(link) {
		
		// State of edit row. Either "edit" when converting row to editable fields, or
		//  "save" when updating the row with changes
		var action = (jQuery(link).is('.tsToggleEdit')) ? 'save' : 'edit';
		
		var row = jQuery("../../td",link);
		var key = jQuery(defaults.ROW_KEY_SELECTOR,row).text();
		
		// add/inherit header classes
		if (defaults.COL_APPLYCLASS) 
			jQuery.tableEditor.lib.addClasses(row,defaults.COLUMN_CLASSES);
		
		// filter out any noedit links
		if (defaults.COLUMN_NOEDIT.length > 0) 
			jQuery.tableEditor.lib.filterNoEdit(row,defaults.COLUMN_NOEDIT);
		
		// filter out originating "edit link" column from row
		//   note; execute this after extracting key.
		row.not(jQuery(defaults.EVENT_LINK_SELECTOR, row).parent()[0]);
		
		// initialize object to be passed to callback functions
		o = {"row": row, "key" : key };
		
		if (action == 'edit') {
			if (defaults.FUNC_PRE_EDIT)
				eval (defaults.FUNC_PRE_EDIT+"(o)");
			
			jQuery(link).addClass('tsToggleEdit').html(defaults.SAVE_HTML);
			
			// Disable sorting on table
			jQuery.tableSorter.active.set(true);
			
			// Convert table row cells into editable form fields.
			row.each(function(i) {
				var html = jQuery.tableEditor.lib.makeEditable(jQuery(this), defaults.COLUMN_NAMES[i], key);
					if (html)
						jQuery(this).html(html);
			
			});
			
			if (defaults.FUNC_POST_EDIT)
				eval (defaults.FUNC_POST_EDIT+"(o)");
		}
		
		if (action == 'save') {
			if (defaults.FUNC_PRE_SAVE)
				eval (defaults.FUNC_PRE_SAVE+"(o)");
			
			jQuery(link).removeClass('tsToggleEdit').html(defaults.EDIT_HTML);
			
			// Enable sorting on table
			jQuery.tableSorter.active.set(true);
			
			// Make cells non editable, update their value.
			row.each(function(i) {	
				var html = jQuery.tableEditor.lib.makeStatic(jQuery(this), defaults.COLUMN_NAMES[i], key);
				if (html)
					jQuery(this).html(html);
			});
		
			// Clear tableSorter's cache (so that ir re-reads row's new/updated values)
			// TODO: RE-DO this using tS's new bind event -- preferably only update cache for only this row
			jQuery.tableSorter.clearCache.set(true);
						
			o.changed = jQuery.tableEditor.cache.row[key];
			o.original = jQuery.tableEditor.cache.original[key];
			if (defaults.FUNC_UPDATE)
				eval (defaults.FUNC_UPDATE+"(o)");	
		}
		return;
		
	}
	
	// DEFAULT CONSTRUCTOR
	return this.each(function(){
		// lookup the name values of the header tables
		var firstRow = this.rows[0];
		var secondRow = this.rows[1];
		var l = firstRow.cells.length;
		
		for( var i=0; i < l; i++ ) {		
			var name = jQuery(firstRow.cells[i]).attr('name');
			
			//OLD: defaults.COLUMN_NAMES[i] = (name) ? name : 'column'+i;
			//NEW: push name on in order it was recieved. this is so the indexes match the columns of the filtered row creaded by editEvent
			defaults.COLUMN_NAMES.push((name) ? name : 'column'+i);
			
			if (jQuery(firstRow.cells[i]).is(defaults.COL_NOEDIT_SELECTOR)) {
				// check for noEdit selector
				defaults.COLUMN_NOEDIT.push(i);
				defaults.COLUMN_NAMES.pop();
			}
			else if (
				typeof(secondRow) != 'undefined' &&
				jQuery(defaults.EVENT_LINK_SELECTOR, secondRow.cells[i]).size() > 0
				) {
				// if this column contains the edi
				defaults.COLUMN_NAMES.pop();
			}
			else if (defaults.COL_APPLYCLASS) { 
				// check for class inheritance
				defaults.COLUMN_CLASSES[i] = jQuery(firstRow.cells[i]).attr('class');
			}
			
		}
		
		// define & assign edit event to each "edit link"
		jQuery(defaults.EVENT_LINK_SELECTOR, this).each(function() {	
			jQuery(this).click(function() {				
				editRow(this);
				return false;
			});
		});
	});
};

jQuery.tableEditor = {
	cache: {
		// When "edit" is clicked; holds a the values of cells in row[key].
		// Upon "save", name/value pair is removed if UNCHANGED, or left alone if changed. 
		//   The object can then be sent as JSON via an AJAX request to the datasource updater
		row: { },
		original: { },
		add: function(key, name, val) {
			if (!this.row[key]) { this.row[key] = { }; }
			this.row[key][name] = val;
		},
		update: function(key, name, val) {
			this.remember(key,name); // todo -> remember only changed?
			// remove from cache upon "match" -- filters row{} of unchanged data
			if (this.row[key][name] == val)
				delete this.row[key][name];	
			else 
				this.row[key][name] = val;
		},
		remember: function(key,name) {
			// copy a rows values
			//  (remembers original value to fall back to in case update fails)
			if (!this.original[key]) { this.original[key] = { }; }
			this.original[key][name] = this.row[key][name];
		}
	},
	lib: {		
		// makes a table cell editable
		// accepts a jQ object (content of cell)
		// accepts a name (str) [will be used as INPUT name attribute]
		// accepts a row key (str) [passed to cache function, so that we have unique row(key):name pairs]
		// returns HTML (editable cell content)
		makeEditable: function(html, name, key) { 
			// determine if html is already a form element
			if (jQuery("input,select,textarea",html).size() > 0) {			
				html = html.find("input,select,textarea"); // constrains jQ object to INPUT vs TD			
				var val = html.val();
				// add preserve class, remove disabled (if set)
				html.attr("disabled", false).addClass("tsPreserve");
				return false;
			}			
			else {
				var val = html.html().replace(/[\"]+/g,'&quot;'); // replace " with HTML entity to behave within value=""
				html = '<input type="text" name="'+name+'" value="'+val+'"></input>';
			}
			jQuery.tableEditor.cache.add(key, name, val);
			return html;
		},
		// makes a table cell static (non editable)
		// accepts a jQ object (content of cell)
		// accepts a name (str) [will be used as INPUT name attribute]
		// accepts a row key (str) [passed to cache function, so that we have unique row(key):name pairs]
		// returns HTML (non editable cell content)
		makeStatic: function(html, name, key ) {
			html = html.find("input,select,textarea"); // constrains jQ object to INPUT vs TD			
			html.attr('disabled', true);
			var val = (html.attr("type") == 'checkbox') ?
				html.is(":checked") :
				html.val();
			
			// update the cache with new value.
			jQuery.tableEditor.cache.update(key, name, val);
			
			return (html.is(".tsPreserve")) ? false : val;
		},
		addClasses: function(row, classes) {
			row.each(function(i) {
				if (typeof(classes[i]) != 'undefined' && classes[i].toString() != '') 
					$(this).addClass(classes[i]); 
			});
		},
		filterNoEdit: function(row, noEdits) {
			var remove = new Array();
			row.each(function(i) {
				for (z=0; z < noEdits.length; z++) {
					if (i == noEdits[z]) 
						remove.push(this);
				}
			});
			for (i=0; i < remove.length; i++)
				row.not(remove[i]);
		}
	}
};
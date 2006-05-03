<?php // Brice's form class. See the example ../bform.php on how to create and print forms. Validation rules
// can be found in validateForm() of this class. Only drawback is that it relies on javascript to clear
// unchanged default values. If the client doesn't support javascript, the value won't be automatically
// cleared upon field focus, and will also persist upon submit if the user doesn't change it. This
// could potentially lead to false positives of "required" fields. A simple workaround is to leave the
// default value blank when creating a field.

// Todo: 
//		allow for multiple forms to exist on one page (prefix form fields with formname)
//		verify that that 'req' validation algorithm works for checkboxes / radio buttons
//     expand rule expressions to include phone, name, fullname, address etc. etc.

// PHASE OUT

class bForm {

	var $_name; // string designating the name of the form
	var $_action; // string holding the designated action url of the form
	var $_method;

	var $_current; // string designating current field
	var $_field; // array to hold form field(s) and their attributes

	var $_valid; // boolean value, true if form has been validated.
	var $_submitted; // boolean value, true if form has been submitted.

	var $_input; // array holding this forms user input.
	var $_errors; // array holding error messages

	// default constructior (initializes bForm variables when new bForm(); is called)
	function bForm($name = "bForm", $action = NULL, $method = "POST") {

		if (empty ($action))
			$action = $_SERVER['PHP_SELF'];

		$this->_name = $name;
		$this->_action = $action;
		$this->_method = $method;

		$this->_current = "";
		$this->_field = array ();

		$this->_valid = false;
		$this->_submitted = false;

		$this->_input = array ();
		$this->_errors = array ();

		// check to see if the form has been submitted, and point the _input array to user data if so.
		switch ($method) {
			case "POST" :
				if (!empty ($_POST[$name.'-submit']))
					$this->_submitted = true;
				// populate _input array with user input supplied to this form. This array is used in form validation.
				$this->_input = & $_POST;
				break;
			case "GET" :
				if (!empty ($_GET[$name.'-submit']))
					$this->_submitted = true;
				// populate _input array with user input supplied to this form. This array is used in form validation.
				$this->_input = & $_GET;
				break;
			default :
				echo "Invalid method in startForm(name,action,method) - must either be \"POST\" or \"GET\"";
				return false;
		}

		return true;
	}
	
	function str2display(& $string) {
		if (!get_magic_quotes_gpc())
			return nl2br(htmlspecialchars($string));
		return nl2br(htmlspecialchars(stripslashes($string)));
	}

	function startForm() {
		echo '<form id="'.$this->_name.'" name="'.$this->_name.'" action="'.$this->_action.'" method="'.$this->_method.'">';
		return;
	}

	function endForm($buttonVal = NULL, $reqBool = TRUE) {
		
		if (empty($buttonVal))
			$buttonVal = "Send Form";
			
		echo "\t<div>\n\t\t<input class=\"button\" id=\"".$this->_name."-submit\" name=\"".$this->_name."-submit\" type=\"submit\" value=\"".$buttonVal."\" /></div>\n";
		
		if ($reqBool) 
			echo "\n\t\t<p><small>Fields marked with <span class=\"required\">*</span> are required.</small></p>";
		
		echo "\n\t</form>";
	}

	function addError(& $rulestr, & $field) {
		// sets _errors["field"] to the string after the ":" in $rulestr
		$pos = strpos($rulestr, ':') + 1;
		$this->_errors["$field"] = substr($rulestr, $pos);
	}

	function trimInput() {
		foreach (array_keys($this->_input) as $key) {
			if (!is_array($this->_input[$key]))
				$this->_input[$key] = trim($this->_input[$key]);
		}
	}

	function inputClear() {
		$this->_input = array('0');
	}
	
	// returns array of user input. If given an array as a parameter, appends to this array.
	function & inputSave($a = NULL) {
		if (!is_array($a))
			$a = array ();
		foreach (array_keys($this->_input) as $key) {
			$value = & $this->_input[$key];
			if (!is_array($value) && $key != $this->_name."-submit")
				$a[$key] = $value;
		}
		return $a;
	}
	

	// loads saved input into _input array. Does not overwirte previously existing POST/GET data
	function inputLoad(& $array) {
		if (!empty ($array)) {
			foreach (array_keys($array) as $key) {
				$value = & $array[$key];

				if (empty ($this->_input[$key]))
					$this->_input[$key] = $value;
			}
		}
	}

	function validateForm() {

		// check if form has been subitted (if there's anything to validate)
		if ($this->_submitted) {

			// check if a validation array exists. If not, return "as valid".
			if (empty ($this->_input[$this->_name.'-validate']))
				return true;
				
			$this->trimInput();

			// loop through each entry in the validation array
			foreach (array_keys($this->_input[$this->_name.'-validate']) as $field) {
				// $rule =& $this->_input[$this->_name.'-validate'][$field]; -- like req || opt  .  rule  :  error message

				// check if a required field is empty/blank/not checked  -- and that this field is not supposed to match another...
				if (empty ($this->_input[$field]) && substr($this->_input[$this->_name.'-validate'][$field], 0, 8) != "opt.same") {
					if (substr($this->_input[$this->_name.'-validate'][$field], 0, 3) == "req")
						$this->addError($this->_input[$this->_name.'-validate'][$field], $field);
					continue;
				}

				// check to see if a rule is called for (if 4th posistion is a period, a rule exists)
				$rule = "";
				if (substr($this->_input[$this->_name.'-validate'][$field], 3, 1) == ".")
					$rule = substr($this->_input[$this->_name.'-validate'][$field], 4, strpos($this->_input[$this->_name.'-validate'][$field], ':') - 4);

				switch ($rule) {
					case "" : // rule is empty, skip.
						break;
					case "url" : // input should match a valid url
						if (!preg_match('|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->_input[$field]))
							$this->addError($this->_input[$this->_name.'-validate'][$field], $field);
						break;
					case "email" : // input should match an email address
						if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $this->_input[$field]))
							$this->addError($this->_input[$this->_name.'-validate'][$field], $field);
						break;
					case "posInt" : // input must be numeric and positive. Value must be a whole # interger
						if (is_numeric($this->_input[$field]) && ($this->_input > 0) && (intval($this->_input[$field]) == $this->_input[$field]))
							$this->_input[$field] = intval($this->_input[$field]);
						else
							$this->addError($this->_input[$this->_name.'-validate'][$field], $field);
						break;
						
						case "numeric" : // input must be numeric
						if (!is_numeric($this->_input[$field]))
							$this->addError($this->_input[$this->_name.'-validate'][$field], $field);
						break;
					case "same" :
						// get error string (should look like "req.same:field:Field does not match"
						$str = substr($this->_input[$this->_name.'-validate'][$field], strpos($this->_input[$this->_name.'-validate'][$field], ':') + 1);
						$pos = strpos($str, ':');
						$match = substr($str, 0, $pos);
						$error = substr($str, $pos);

						if ($this->_input[$field] != $this->_input[$match])
							$this->addError($error, $field);
						break;
					default :
						echo "<br> $field HAS UNKNOWN RULE: $rule -- contact administrator";
						die;
				}
			}

			if (empty ($this->_errors))
				$this->_valid = true;
		}

		return $this->_valid;
	}

	function newField($name) {
		$this->_field[$name]['name'] = $name;
		$this->_current = $name;
	}

	function newFieldSet($name) {
		echo "\n  <fieldset>\n    <legend>$name</legend>\n";
	}
	function endFieldSet() {
		echo "\n  </fieldset>\n ";
	}

	function setField($attr, $str) {
		$this->_field[$this->_current][$attr] = $str;
	}

	function printField($name = NULL) {

		if (empty ($name))
			$name = $this->_current;

		// print label
		echo "\n\t\t<div class=\"field\">\n\t\t\t <label for=\"$name\">{$this->_field[$name]['prompt']}</label>\n";
		// create field value string
		if (!empty ($this->_input[$name])) // use user submitted data if it exists
			$valStr = $this->str2display($this->_input[$name]);
		elseif (!empty ($this->_field[$name]['init'])) // or else use hardcoded initial value
			$valStr = htmlspecialchars($this->_field[$name]['init']);
		else // or use default
			$valStr = htmlspecialchars($this->_field[$name]['default']);
			
		if (empty($this->_field[$name]['misc']))
		$this->_field[$name]['misc'] = '';

		// print field
		switch ($this->_field[$name]['type']) {
			case "text" :
				echo "\n\t\t<input type=\"text\" class=\"text\" name=\"$name\" id=\"$name\" title=\"{$this->_field[$name]['default']}\" value=\"$valStr\" {$this->_field[$name]['misc']} />\n";
				break;

			case "textarea" :
				echo "\n\t\t<input type=\"hidden\" id=\"".$name."_maxlength\" value=\"{$this->_field[$name]['maxlen']}\" />\n";
				echo "\n\t\t<textarea {$this->_field[$name]['misc']} id=\"$name\" name=\"$name\" title=\"{$this->_field[$name]['default']}\" />$valStr</textarea>\n";
				break;

			case "checkbox" :
				// this hidden element is necessary for the state (checked/unchecked) of the checkbox to be saved.
				// if left out, and a box is checked.. the box can never be unchecked, as it's "checked" state will persist.
				// the hidden element causes a NULL value to be assigned to $name, and therefore the old state is overwritten.'
				echo "\n\t\t<input type=\"hidden\" name=\"$name\" value=\"off\">";
				if ($valStr == "on" || $valStr == "checked")
					echo "\n\t\t<input type=\"checkbox\" class=\"checkbox\" name=\"$name\" id=\"$name\" checked {$this->_field[$name]['misc']}/>\n";
				else
					echo "\n\t\t<input type=\"checkbox\" class=\"checkbox\" name=\"$name\" id=\"$name\" {$this->_field[$name]['misc']}/>\n";
				break;

			case "select" :
				echo "\n\t\t <select name=\"$name\" id=\"$name\" {$this->_field[$name]['misc']} />\n";

				foreach (array_keys($this->_field[$name]['option']) as $key) {
					$str = & $this->_field[$name]['option'][$key];
					if ($pos = strpos($str, ':')) {
						$val = substr($str, 0, $pos);
						$desc = substr($str, $pos +1);
					} else {
						$val = $str;
						$desc = $str;
					}
					if ($val == $valStr)
						echo '			<option value="'.htmlspecialchars($val).'" SELECTED>'.$desc.'</option>';
					else
						echo '			<option value="'.htmlspecialchars($val).'">'.$desc.'</option>';

				}
				echo "\n\t\t</select>\n";

				break;

			default :
				echo "bad field type, contact administrator!";
		}

		// print notes
		if (!empty ($this->_field[$name]['notes']))
			echo "<div class=\"notes\">{$this->_field[$name]['notes']}</div>\n";

		//print error
		if (!empty ($this->_field[$name]['validate']))
			echo "\n\t\t<input type=\"hidden\" name=\"$this->_name-validate[$name]\" value=\"{$this->_field[$name]['validate']}:{$this->_field[$name]['error']}\" \>\n";
		//print footer
		echo "\n\t\t</div>\n\t\t<br />\n";
	}

	function printErrors() {
		if (!empty ($this->_errors)) {
			echo "\n<div class=\"formerror\">\n <h1> Problems with your submission </h1>\n";
			echo "<ul>\n";
			foreach ($this->_errors as $field => $row) {
				echo "<li>".$this->str2display($row)."  <label for=\"".$field."\"><span class=\"focus\">(focus)</span></label></li>\n";
			}
			echo "</ul></div>\n";
		}
	}

}
?>
<?php

//-------------------------------------------------------------------- FUNCTIONS

function test_lists($window)
{
	// ListView

	$list = wb_get_control($window, IDC_LISTVIEW2027);
	wb_set_text($list, array(
		array("Home", 120),
		"Status",
		array("More", 50),
		array(0, 80)
	));
	wb_create_items($list, array(
		array("0", "First", "Done", "Less"),
		array(0, 0, "Second", "Hi"),
		array(200, 300, "Hi!", 876),
	));
	wb_set_text($list, array(444, 555, 666));

//	wb_set_style($list, WBC_CHECKBOXES);
	wb_set_image($list, PATH_RES . "treeview.bmp", 0, GREEN, 10);
//	wb_set_item_image($list, array(1, 1), 4);
//	wb_delete_items($list, 0);
	wb_set_value($list, array(1,2));
	wb_set_value($list, array(1,3));
//	wbtemp_set_listview_item_checked($list, 0, 1);

	// ListView II

	$list = wb_get_control($window, IDC_LISTVIEW2067);
	wb_set_text($list, array("No grid lines","on this control!"));
	wb_create_items($list, array(
		array("To do this,", "set"),
		array("the control style", "to WS_BORDER"),
	));

	// List boxes / combo boxes

	wb_set_text(wb_get_control($window, IDC_LISTBOX2018),
		"List 1\r\nOne more\rAnother\nEtc.\r\nMore...");
	wb_set_text(wb_get_control($window, IDC_COMBOBOX2010),
		array("Option 1","Option 2","More","Etc."));

	wb_set_value(wb_get_control($window, IDC_UPDOWN2069), null, 0, 3);

	// Handler

//	wb_set_handler($window, "process_test_lists");
}

// Window handler: return TRUE if control was already processed

function process_test_lists($window, $id, $ctrl=0, $lparam=0)
{
//	$ctrl = wb_get_control($window, $id);

	switch($id) {

		case IDC_LISTLINE:
			$n = wb_get_value($ctrl);
			$text = wb_get_text(wb_get_control($window, IDC_LISTVIEW2027), $n);
			if(is_array($text)) {
				$elem = $text[1];
				$text = implode(" ", $text);
			}
			wb_set_text(wb_get_control($window, IDC_EDIT2064), "$n [$text]\r\n$elem");
			return true;

		case IDC_LISTVIEW2027:
		case IDC_LISTVIEW2067:

			if($lparam == WBC_DBLCLICK) {
				wb_set_text(wb_get_control($window, IDC_EDIT2064), "Double-clicked.");
			} else {
				$val = wb_get_value($ctrl);
				if($val && is_array($val))
					$val = implode(", ", $val);
				$sel = wb_get_selected($ctrl);
				if($sel && is_array($sel))
					$sel = implode(", ", $sel);
				$text = wb_get_text($ctrl);

				if(@is_array($text[0])) {
					$all = $text;
					$text = "";
					foreach($all as $arr)
						$text .= "(" . implode(", ", $arr) . ") ";
				} elseif(is_array($text))
					$text = implode(", ", $text);
				wb_set_text(wb_get_control($window, IDC_EDIT2064), "Selected rows: $sel\nValues: [$val]\nText: $text");
			}
			return true;

		case IDC_LISTBOX2018:
		case IDC_COMBOBOX2010:
			$val = wb_get_value($ctrl);
			$text = wb_get_text($ctrl);
			wb_set_text(wb_get_control($window, IDC_EDIT2064), "[$text] [val: $val]");
			return true;
	}
	return false;
}

//-------------------------------------------------------------------------- END

?>
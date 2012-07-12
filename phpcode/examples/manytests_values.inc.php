<?php

//-------------------------------------------------------------------- FUNCTIONS

function test_values($window)
{
	wb_set_value(wb_get_control($window, IDC_HSCROLL4026),		50, 0, 10000);
	wb_set_value(wb_get_control($window, IDC_UPDOWN4028),		10, 0, 50);
	wb_set_value(wb_get_control($window, IDC_CHECKBOX4034), 	TRUE);
	wb_set_value(wb_get_control($window, IDC_RADIOBUTTON4015),	TRUE);

	wb_set_range(wb_get_control($window, IDC_HSCROLL4056),		0, 400);
	wb_set_range(wb_get_control($window, IDC_SLIDER4011),		0, 200);
	wb_set_range(wb_get_control($window, IDC_PROGRESSBAR4007),	0, 60);

	// List boxes / combo boxes

	wb_set_text(wb_get_control($window, IDC_LISTBOX4053),
		"Option1\nOption2\nOption3");
	wb_set_text(wb_get_control($window, IDC_COMBOBOX4054),
		"Option1\nOption2\nOption3");
	wb_set_text(wb_get_control($window, IDC_LISTBOX4055),
		"Option1\nOption2\nOption3");

	// Edit_boxes

	$text = wb_get_text(wb_get_control($window, IDC_EDIT4057));
	wb_set_text(wb_get_control($window, IDC_EDIT4058), $text);
	wb_set_text(wb_get_control($window, IDC_EDIT4059), $text);
	wb_set_text(wb_get_control($window, IDC_EDIT4065), implode(" ", array_fill(0, 20, $text)));
}

// Window handler

function process_test_values($window, $id, $ctrl=0, $lparam1=0, $lparam2=0)
{
	global $statusbar;

	if($lparam1 == WBC_GETFOCUS) {
		wb_set_text($statusbar, "Focus to control $id");
		return false;
	}

	switch($id) {

		case IDC_EDIT4069:
		case IDC_EDIT4071:
			if($lparam1 == WBC_KEYDOWN)
				wb_set_text($statusbar, "Char $lparam2: " . chr($lparam2));
			break;

		case IDC_EDIT4057:
			if($lparam1 == WBC_KEYDOWN) {
				wb_set_text($statusbar, "Char $lparam2: " . chr($lparam2));
//				wb_set_text(wb_get_control($window, IDC_STATIC4048), $lparam2 . "\n" . chr($lparam2));
				break;
			}
			$text = wb_get_text(wb_get_control($window, IDC_EDIT4057));
			wb_set_text(wb_get_control($window, IDC_EDIT4058), $text);
			wb_set_text(wb_get_control($window, IDC_EDIT4059), $text);
			wb_set_text(wb_get_control($window, IDC_EDIT4065), implode(" ", array_fill(0, 20, $text)));
			break;

		case IDC_LISTBOX4053:
			if($lparam1 & WBC_DBLCLICK)
				wb_set_text(wb_get_control($window, IDC_STATIC4048), "Double-click");
			else
				wb_set_text(wb_get_control($window, IDC_STATIC4048), "Single click");
			break;

		case IDC_HSCROLL4056:
		case IDC_SLIDER4011:
			$value = wb_get_value(wb_get_control($window, $id));
			wb_set_value(wb_get_control($window, IDC_PROGRESSBAR4007), $value);
			wb_set_text(wb_get_control($window, IDC_STATIC4019), $value);
			wb_set_value(wb_get_control($window, IDC_HSCROLL4026), $value);
			wb_set_value(wb_get_control($window, IDC_VSCROLL4051), $value);
			wb_set_value(wb_get_control($window, IDC_UPDOWN4028), $value);
			$value = wb_get_value(wb_get_control($window, $id));
			wb_set_text(wb_get_control($window, IDC_STATIC4048), "Control #$id: value [$value]\n");
			wb_set_value(wb_get_control($window, IDC_EDIT4052), $value);
			break;

		case IDC_RADIOBUTTON4014:
		case IDC_RADIOBUTTON4015:
		case IDC_RADIOBUTTON4016:
			$value = $id - IDC_RADIOBUTTON4014;
			wb_set_value(wb_get_control($window, IDC_LISTBOX4053), $value);
			wb_set_value(wb_get_control($window, IDC_COMBOBOX4054), $value);
			wb_set_value(wb_get_control($window, IDC_LISTBOX4055), $value);
			break;

		case IDC_CHECKBOX4034:
			wb_set_selected(wb_get_control($window, IDC_CHECKBOX4049),
				wb_get_selected($ctrl));
			$value = wb_get_value(wb_get_control($window, $id));
			wb_set_text(wb_get_control($window, IDC_STATIC4048),
				"Control #$id: value [$value]\n");
			break;

		case IDC_UPDOWN4028:
		case IDC_CHECKBOX4049:
			$value = wb_get_value(wb_get_control($window, $id));
			wb_set_text(wb_get_control($window, IDC_STATIC4048),
				"Control #$id: value [$value]\n");
			break;
	}
}

//-------------------------------------------------------------------------- END

?>
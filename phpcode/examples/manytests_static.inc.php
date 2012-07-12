<?php

//-------------------------------------------------------------------- FUNCTIONS

function test_static($window)
{
}

// Window handler

function process_test_static($window, $id, $ctrl=0, $lparam=0)
{
	switch($id) {

		case IDC_BUTTON5068:
			$ctrl = wb_get_control($window, IDC_STATIC5062);
			wb_set_image($ctrl, PATH_RES . "hyper.ico");
			wb_set_style($ctrl, 0, 2);
//			wb_set_style($ctrl, 0x00020000, true);
			$ctrl = wb_get_control($window, IDC_STATIC5063);
			wb_set_image($ctrl, PATH_RES . "up_arrow.bmp", 0, GREEN);
			wb_set_style($ctrl, 0);
//			wb_set_style($ctrl, 0x00000000, true);
			return true;
	}
	return false;
}

//-------------------------------------------------------------------------- END

?>
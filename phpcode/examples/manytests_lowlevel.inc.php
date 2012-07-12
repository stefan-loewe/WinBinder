<?php

//-------------------------------------------------------------------- CONSTANTS

define("WBOBJ",			"Vhwnd/Vid/Vuclass/litem/lsubitem/Vstyle/Vparent/Vhandler/Vlparam/V8lparams/Vpbuffer");
define("WBOBJ_RAW",		"V3l2V13");
define("WBOBJ_SIZE",	72);

//-------------------------------------------------------------------- FUNCTIONS

function test_lowlevel($window)
{
	global $maintab;

	$result = "TEST LOW-LEVEL FUNCTIONS\n\n";

	$result .= "Peek/poke a string\n";

		$str = "Test string.";
		$addr = wb_get_address($str);
		$result .= wb_peek($addr) . "\n";
		wb_poke($addr, "Roo");
		$result .= wb_peek($addr) . "\n";
		$result .= "$str\n\n";

	$result .= "Peek/poke a long integer\n\n";

		$num = 1234;
		$array = (unpack("Vnum", wb_peek(wb_get_address($num), 4)));
		$result .= "{$array['num']}\n";
		wb_poke(wb_get_address($num), pack("V", 800), 4);
		$array = (unpack("Vnum", wb_peek(wb_get_address($num), 4)));
		$result .= "{$array['num']}\n";
		$result .= "$num\n\n";

	$result .= "Peek/poke a double float\n\n";

		$num = 100.01;
		$array = (unpack("dnum", wb_peek(wb_get_address($num), 8)));
		$result .= "{$array['num']}\n";
		wb_poke(wb_get_address($num), pack("d", 800.02), 8);
		$array = (unpack("dnum", wb_peek(wb_get_address($num), 8)));
		$result .= "{$array['num']}\n";
		$result .= "$num\n\n";

	$result .= "Peek/poke control data\n\n";

		$id = ID_RESETHANDLER;
		$result .= "Getting control handle...\n";
		$ctrl = wb_get_control($window, $id);
		$wbobj = unpack(WBOBJ, wb_peek($ctrl, WBOBJ_SIZE));
		$result .= sprintf("Original item # is %d\n", $wbobj["item"]);
		$result .= "Changing the value of member 'item'...\n";
		// The offset of member 'item' in struct WBOBJ is 12
		wb_poke($ctrl + 12, pack("V", 1200), 4);
		$wbobj = unpack(WBOBJ, wb_peek($ctrl, WBOBJ_SIZE));
		$result .= sprintf("Current item # is now %d\n", $wbobj["item"] . "\n");

	$result .= "Peek/poke window callback\n\n";

		$result .= "Current window data:\n";
		$wbobj = unpack(WBOBJ, wb_peek($window, WBOBJ_SIZE));
		$result .= print_r($wbobj, true);
		$orig_address = wb_get_address($wbobj["handler"]);
		$orig_handler = wb_peek($wbobj["handler"]);
		$result .= "Name of original window handler: \"$orig_handler\"\n";
		$result .= "Changing handler using wb_poke()...\n";
		$newname = "dummy_callback";
		// The offset of member 'pszCallBackFn' in struct WBOBJ is 28
		wb_poke($window + 28, pack("V", wb_get_address($newname)), 4);
		$wbobj = unpack(WBOBJ, wb_peek($window, WBOBJ_SIZE));
		$handler = wb_peek($wbobj["handler"]);
		$result .= "Window handler is now \"$handler\"\n\n";

	$result .= "END TESTS\n\n";

		wb_set_text(wb_get_control($maintab, IDC_EDIT6002), $result);
}

// Dummy callback function

function dummy_callback($window, $id)
{
	switch($id) {
		case ID_RESETHANDLER:
			wb_set_text(wb_get_control($window, IDC_EDIT6002),
				"Resetting window handler...");
			wb_set_handler($window, "process_main");
			break;
	}
}

// Window handler

function process_test_lowlevel($window, $id)
{
	switch($id) {
		case ID_LOWLEVEL:
			test_lowlevel($window);
			break;
	}
	return false;
}

//-------------------------------------------------------------------------- FIM

?>
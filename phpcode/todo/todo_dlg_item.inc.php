<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ToDo application - Item dialog

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function create_edit_dlg($parent, $id, $cat=null)
{
	global $id_edit, $curr_cat;

	$id_edit = $id;
	$curr_cat = $cat;

	// Remove the line below while not under development

	file_put_contents("dlg_item.rc.php", "<?php\n\n" . parse_rc(file_get_contents(PATH_DATA . "dlg_item.rc"),
	  '$dlg', '$parent', 'ModalDialog', null, WBC_CENTER, WBC_CENTER, WBC_CENTER, WBC_CENTER, 'WBC_INVISIBLE') . "\n?>");

	include("dlg_item.rc.php");
	wb_set_handler($dlg, "process_item");

	wb_set_text($dlg, $id ? "Edit item #$id" : "Create new item");

	// Fill up the lists

	$item_data = db_get_data("cat", null, array("name"));
	wb_set_text(wb_get_control($dlg, ID_CATLIST), $item_data);

	$item_data = db_get_data("priority", null, array("name"));
	wb_set_text(wb_get_control($dlg, ID_PRIORLIST), $item_data);

	$item_data = db_get_data("severity", null, array("name"));
	wb_set_text(wb_get_control($dlg, ID_SEVERLIST), $item_data);

	update_item_controls($dlg);
	wb_set_visible($dlg, true);
}

function process_item($window, $id)
{
	global $mainwin;

	switch($id) {

		case IDOK:
			if(update_item($window)) {
				wb_destroy_window($window);
				update_items($mainwin);
			}
			break;

		case IDCANCEL:
		case IDCLOSE:
			wb_destroy_window($window);
			break;
	}
}

/* Update controls */

function update_item_controls($window)
{
	global $id_edit, $curr_cat;

	$table = "item";
	$data = db_get_data($table, $id_edit, null, "", FETCH_ASSOC);
	$data = $data[0];
	wb_set_text(wb_get_control($window, ID_NAME), $data["name"]);
	wb_set_text(wb_get_control($window, ID_DESCRIPTION), $data["description"]);

	// Select the appropriate values from the combo boxes

	wb_set_selected(wb_get_control($window, ID_CATLIST), (int)array_search(($curr_cat === null) ?
		$data["cat"] : $curr_cat, db_get_data("cat", null, "id")));

	wb_set_selected(wb_get_control($window, ID_PRIORLIST), (int)array_search($data["priority"],
	  db_get_data("priority", null, "id")));

	wb_set_selected(wb_get_control($window, ID_SEVERLIST), (int)array_search($data["severity"],
	  db_get_data("severity", null, "id")));
}

function update_item($window)
{
	global $id_edit;

	$name = wb_get_text(wb_get_control($window, ID_NAME));
	$descr = wb_get_text(wb_get_control($window, ID_DESCRIPTION));

	// Fetch the id from table 'cat'

	$id_cat = (int)db_get_id("cat", wb_get_selected(wb_get_control($window, ID_CATLIST)));
	$id_prior = (int)db_get_id("priority", wb_get_selected(wb_get_control($window, ID_PRIORLIST)));
	$id_sever = (int)db_get_id("severity", wb_get_selected(wb_get_control($window, ID_SEVERLIST)));

	$res = db_edit_record("item", $id_edit, "name\ndescription\ncat\npriority\nseverity",
		array($name, $descr, $id_cat, $id_prior, $id_sever));
	if(!$res)
		wb_message_box($window, "Problem editing this record.", null, WBC_WARNING);
	return $res;
}

//------------------------------------------------------------------ END OF FILE

?>

<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ToDo application - Category dialog

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function create_cat_dlg($parent)
{
	// Remove the line below while not under development

	file_put_contents("dlg_cat.rc.php", "<?php\n\n" . parse_rc(file_get_contents(PATH_DATA . "dlg_cat.rc"),
	  '$dlg', '$parent', 'ModalDialog') . "\n?>");

	include("dlg_cat.rc.php");
	wb_set_handler($dlg, "process_cat");
	wb_set_image(wb_get_control($dlg, ID_TREE), PATH_RES . "treeview.bmp", GREEN, 0, 10);

	wb_set_enabled(wb_get_control($dlg, ID_SETITEM), false);
	wb_set_enabled(wb_get_control($dlg, ID_DELETEITEM), false);

	update_cat_controls($dlg);
	update_tree($dlg);

	// Temporarily disabled

	wb_set_text($dlg, "Edit categories (TEMPORARILY DISABLED)");
}

function process_cat($window, $id)
{
	global $it_list, $mainwin, $cat_table;

	switch($id) {

		case ID_TREE:
			update_cat_controls($window);
			break;

		case ID_NEWITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			$level = wb_get_selected(wb_get_control($window, ID_LEVEL));
//			db_edit_record($cat_table, 0, "name\nlevel", array($name, $level));
			update_tree($window);
			update_cat_controls($window);
			break;

		case ID_SETITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			$level = wb_get_selected(wb_get_control($window, ID_LEVEL));
			$id = get_selected_tree_item_id($window);
//			db_edit_record($cat_table, $id, "name\nlevel", array($name, $level));
			update_tree($window);
			update_cat_controls($window);
			break;

		case ID_DELETEITEM:
			$selected = get_selected_tree_item_index($window);
//			db_delete_records($cat_table, db_get_id($cat_table, $selected));
			update_tree($window);
			update_cat_controls($window);
			break;

		case ID_MOVEUP:
			$selected = get_selected_tree_item_index($window);
//			db_swap_records($cat_table, db_get_id($cat_table, $selected),
//				db_get_id($cat_table, $selected - 1));
			update_tree($window);
			update_cat_controls($window);
			break;

		case ID_MOVEDOWN:
			$selected = get_selected_tree_item_index($window);
//			db_swap_records($cat_table, db_get_id($cat_table, $selected),
//				db_get_id($cat_table, $selected + 1));
			update_tree($window);
			update_cat_controls($window);
			break;

		case IDCANCEL:
		case IDCLOSE:
		case IDOK:
			wb_destroy_window($window);
			update_tree($mainwin);
			break;
	}
}

/* Update controls */

function update_cat_controls($window)
{
	global $mainwin, $cat_table, $it_list;

	// Below: get id of selected treeview element. See comment in update_items()

	$it_list = wb_get_control($window, ID_TREE);
	$sel_text = wb_get_text($it_list);
	$cat_data = db_get_data("cat", null, "name");
	$sel_index = (int)array_search($sel_text, $cat_data);
	$cat_data = db_get_data("cat", null, "id");
	$id_cat = $cat_data[$sel_index];
	$id = db_get_id($cat_table, $sel_index);
	$data = db_get_data($cat_table, $id, null, "", FETCH_ASSOC);
	$data = $data[0];

	wb_set_text(wb_get_control($window, ID_NAME), $data["name"]);
	wb_set_value(wb_get_control($window, ID_LEVEL), $data["level"]);

	wb_set_enabled(wb_get_control($window, ID_SETITEM), $sel_text != "");
	wb_set_enabled(wb_get_control($window, ID_DELETEITEM), $sel_text != "");
}

function get_selected_tree_item_id($window)
{
	global $cat_table, $it_list;

	// Below: get id of selected treeview element. See comment in update_items()

	$it_list = wb_get_control($window, ID_TREE);
	$sel_text = wb_get_text($it_list);
	$cat_data = db_get_data("cat", null, "name");
	$sel_index = (int)array_search($sel_text, $cat_data);
	$cat_data = db_get_data("cat", null, "id");
	$id_cat = $cat_data[$sel_index];
	$id = db_get_id($cat_table, $sel_index);
	return $id;
}

function get_selected_tree_item_index($window)
{
	global $cat_table, $it_list;

	// Below: get id of selected treeview element. See comment in update_items()

	$it_list = wb_get_control($window, ID_TREE);
	$sel_text = wb_get_text($it_list);
	$cat_data = db_get_data("cat", null, "name");
	$sel_index = (int)array_search($sel_text, $cat_data);
	return $sel_index;
}

//-------------------------------------------------------------------------- END

?>

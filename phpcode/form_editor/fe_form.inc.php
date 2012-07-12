<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - form

*******************************************************************************/

//-------------------------------------------------------------------- CONSTANTS

// Constants for the selection handles

define("HANDLE_WIDTH",		6);
define("HANDLE_HEIGHT",		6);
define("HANDLE_XOFFSET",	13);
define("HANDLE_YOFFSET",	13);

define("HANDLE_XOFFSET_5",	15);		// Must be a multiple of the grid
define("HANDLE_YOFFSET_5",	15);		// Must be a multiple of the grid
// Possible mouse positions related to a control

define("NOWHERE",		0);
define("INCONTROL",		1);
define("INNHANDLE",		2);
define("INEHANDLE",		3);
define("INSHANDLE",		4);
define("INWHANDLE",		5);
define("INNWHANDLE",	6);
define("INNEHANDLE",	7);
define("INSEHANDLE",	8);
define("INSWHANDLE",	9);

//-------------------------------------------------------------------- FUNCTIONS

/* Create the form */

function create_form($parent, $title, $width, $height)
{
	global $wb;

	start_drawing_functions();

	// Create the form

	$wb->formwin = wb_create_window($parent, ResizableWindow, $title,
		0, 0, $width, $height,
		WBC_INVISIBLE | WBC_CUSTOMDRAW | WBC_NOTIFY,
		WBC_REDRAW | WBC_RESIZE | WBC_MOUSEDOWN | WBC_MOUSEUP | WBC_MOUSEMOVE | WBC_DBLCLICK);

	// Remove unwanted window buttons

	$style = GetWindowStyle($wb->formwin);
	SetWindowStyle($wb->formwin, $style & ~(WS_MAXIMIZEBOX | WS_MINIMIZEBOX));
	EnableCloseButton($wb->formwin, false);

	// Set more window properties

	wb_set_area($wb->formwin, WBC_MINSIZE, 0, 0, 80, 60);
	wb_set_handler($wb->formwin, "process_form");
	wb_set_image($wb->formwin, PATH_RESPVT . "form.ico");

 	// Initialize form data

	reset_form(DEFAULT_WINCLASS, $parent, $title, $width, $height);
}

/* Reset form and form data, optionally setting some attributes */

function reset_form($class, $parent, $title, $width, $height, $style=0, $value=0)
{
	global $wb;

	$wb->currentform = 0;
	$wb->proj_filename = null;

	if(!isset($wb->form[$wb->currentform]))
		$wb->form[$wb->currentform] = new stdclass;

 	// Reset form data with fixed attributes

	$wb->form[$wb->currentform]->ct = array();
	$wb->form[$wb->currentform]->numcontrols = 0;
	$wb->form[$wb->currentform]->ncurrindex = 0;
	$wb->form[$wb->currentform]->nselcontrol = -1;
	$wb->form[$wb->currentform]->treenode = 0;

	foreach($wb->project_array as $var)
		$wb->form[$wb->currentform]->$var = constant("DEFAULT_" . strtoupper($var));

	// Variable attributes

	$wb->form[$wb->currentform]->width = $width;
	$wb->form[$wb->currentform]->height = $height;
	$wb->form[$wb->currentform]->caption = $title;
	$wb->form[$wb->currentform]->cclass = $class;
	$wb->form[$wb->currentform]->style = (int)$style;
	$wb->form[$wb->currentform]->value = (int)$value;

	// Resize, reposition and show form

	$pos = wb_get_position($parent);
	wb_set_position($wb->formwin, $pos[0] + 180, $pos[1] + 76);
	wb_set_size($wb->formwin, $width, $height);
	wb_set_text($wb->formwin, $title);
	wb_set_visible($wb->formwin, true);

	// Create a treeview item for this form

	wb_delete_items($wb->tree, $wb->form[$wb->currentform]->treenode);

	$root = $wb->rootnode;

	$node = wb_create_items($wb->tree, array(
		array($title, $wb->currentform, $root, 8, 9, 2),
	));

	$wb->form[$wb->currentform]->treenode = $node;

	// numforms is always set to 1

	$wb->numforms = 1;

	// Expand the root and select the new node

	wb_set_state($wb->tree, $root, 1);
	wb_set_selected($wb->tree, $node);
}

/* Form processing */

function process_form($window, $id, $ctrl, $lparam1=0, $lparam2=0)
{
	global $wb;

	static $controls = array();
	static $isMoving = false;
	static $xcuroff = 0;
	static $ycuroff = 0;
	static $ctrl_lastpos = null;

	switch($id) {

		case IDDEFAULT:

			if($lparam1 & WBC_RESIZE) {					// Form was resized

				$size = wb_get_size($wb->formwin);
				$wb->form[$wb->currentform]->width = $size[0];
				$wb->form[$wb->currentform]->height = $size[1];
				update_control_data();

			} elseif($lparam1 & WBC_REDRAW) {			// Redraw screen

				// Draw the form background and grid

				$buffer = $lparam2;
				$dim = wb_get_size($window, true);
				$winwidth = $dim[0];
				$winheight = $dim[1];
				draw_background($buffer, $winwidth, $winheight);

				// Draw all controls from the current form

				for($i = 0; $i < $wb->form[$wb->currentform]->numcontrols; $i++) {
					$ct = $wb->form[$wb->currentform]->ct[$i];
					if($ct)
						draw_control($buffer, $ct);
				}

				// Draw a "ghost"

				if($ctrl_lastpos && $wb->ghost)
					draw_ghost($buffer, $ctrl_lastpos);

				// Is there a control selected?

				if($wb->form[$wb->currentform]->nselcontrol >= 0) {
					$ct = $wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol];
					if(!$isMoving)
						draw_handles($buffer, "", $ct->left, $ct->top, $ct->width, $ct->height);
				} else
					break;

			} elseif($lparam1 & WBC_MOUSEUP) {		// A button was released

				$ctrl_lastpos = null;
				$isMoving = false;
				$wb->mouseop = false;
				$xcuroff = 0;
				$ycuroff = 0;
				wb_refresh($window, true);
				SetMyCursor(IDC_ARROW);
				$wb->mousecpos = NOWHERE;

			} elseif($lparam1 & WBC_LBUTTON) {		// Left button is held down

				$wb->mouseop = true;

				$xleft = $lparam2 & 0xFFFF;
				$ytop = ($lparam2 & 0xFFFF0000) >> 16;

				// Left mouse button was pressed

				if($lparam1 & WBC_MOUSEDOWN) {

					// Can't change the current control while resizing or moving a control

					if($wb->mousecpos == NOWHERE || $wb->mousecpos == INCONTROL) {

						// If mouse cursor is clicked inside a control, select a new one.
						// Search in reverse order because we need the topmost control

						$wb->form[$wb->currentform]->nselcontrol = -1;	// Start with no control selected
						$form = $wb->form[$wb->currentform];			// Auxiliary variable

						// This loop finds and selects a new control as current

						for($i = $form->numcontrols - 1; $i >= 0; $i--) {
							$cttest = $form->ct[$i];
							if(($xleft >= $cttest->left) && ($xleft <= $cttest->left + $cttest->width) &&
							   ($ytop >= $cttest->top) && ($ytop <= $cttest->top + $cttest->height)) {
								$ctrl_lastpos = $form->ct[$i];
								$wb->form[$wb->currentform]->nselcontrol = $i;
								break;
							}
						}

						// No control selected

						update_control_data();
					}
				}

				// $ct (an auxiliary variable) is set to the currently selected control

				if($wb->form[$wb->currentform]->nselcontrol >= 0)
					$ct = $wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol];


				// **** Below: resizes aren't considering cursor offset

				if($wb->mousecpos == INCONTROL) {		// Moving control

					$x = $lparam2 & 0xFFFF;
					$y = ($lparam2 & 0xFFFF0000) >> 16;

					if(!$isMoving) {
						$isMoving = true;
						$xcuroff = $x - $ct->left;
						$ycuroff = $y - $ct->top;
					}

					SetMyCursor(IDC_CROSS);

					// Is grid active?

					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->left = $wb->grid ?
						(int)(max(4, ($x - $xcuroff)) / 5) * 5 : $x - $xcuroff;
					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->top = $wb->grid ?
						(int)(max(4, ($y - $ycuroff)) / 5) * 5 : $y - $ycuroff;
					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INNHANDLE) {		// Resizing control (North)

					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENS);

					$bottom = $ct->top + $ct->height;
					$newtop = $y1 + HANDLE_YOFFSET_5;
					$newheight = $bottom - $newtop;
					if($newheight > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->top = $newtop;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = $newheight;
					}

					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INEHANDLE) {		// Resizing control (East)

					$x = $lparam2 & 0xFFFF;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;

					SetMyCursor(IDC_SIZEWE);

					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = max(1, $x1 - $ct->left);
					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INWHANDLE) {		// Resizing control (West)

					$x = $lparam2 & 0xFFFF;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;

					SetMyCursor(IDC_SIZEWE);

					$right = $ct->left + $ct->width;
					$newleft = $x1 + HANDLE_XOFFSET_5;
					$newwidth = $right - $newleft;
					if($newwidth > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->left = $newleft;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = $newwidth;
					}

					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INSHANDLE) {		// Resizing control (South)

					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENS);

					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = max(1, $y1 - $ct->top);
					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INNEHANDLE) {	// Resizing control (Northeast)

					$x = $lparam2 & 0xFFFF;
					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;
					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENESW);

					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = max(1, $x1 - $ct->left);

					$bottom = $ct->top + $ct->height;
					$newtop = $y1 + HANDLE_YOFFSET_5;
					$newheight= $bottom - $newtop;
					if($newheight > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->top = $newtop;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = $newheight;
					}

					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INSEHANDLE) {	// Resizing control (Southeast)

					$x = $lparam2 & 0xFFFF;
					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;
					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENWSE);

					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = max(1, $x1 - $ct->left);
					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = max(1, $y1 - $ct->top);
					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INNWHANDLE) {	// Resizing control (Northwest)

					$x = $lparam2 & 0xFFFF;
					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;
					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENWSE);

					$right = $ct->left + $ct->width;
					$newleft = $x1 + HANDLE_XOFFSET_5;
					$newwidth = $right - $newleft;
					if($newwidth > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->left = $newleft;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = $newwidth;
					}

					$bottom = $ct->top + $ct->height;
					$newtop = $y1 + HANDLE_YOFFSET_5;
					$newheight= $bottom - $newtop;
					if($newheight > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->top = $newtop;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = $newheight;
					}

					update_control_data();
					wb_refresh($window, true);

				} elseif($wb->mousecpos == INSWHANDLE) {	// Resizing control (Southwest)

					$x = $lparam2 & 0xFFFF;
					$y = ($lparam2 & 0xFFFF0000) >> 16;

					// Is grid active?

					$x1 = $wb->grid ? (int)(max(4, $x - 0) / 5) * 5 : $x;
					$y1 = $wb->grid ? (int)(max(4, $y - 0) / 5) * 5 : $y;

					SetMyCursor(IDC_SIZENESW);

					$right = $ct->left + $ct->width;
					$newleft = $x1 + HANDLE_XOFFSET_5;
					$newwidth = $right - $newleft;
					if($newwidth > 0) {
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->left = $newleft;
						$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->width = $newwidth;
					}
					$wb->form[$wb->currentform]->ct[$wb->form[$wb->currentform]->nselcontrol]->height = max(1, $y1 - $ct->top);
					update_control_data();
					wb_refresh($window, true);
				}

			} elseif(!($lparam1 & (WBC_LBUTTON | WBC_RBUTTON))) {		// No button pressed

				$xleft = $lparam2 & 0xFFFF;
				$ytop = ($lparam2 & 0xFFFF0000) >> 16;

				$form = $wb->form[$wb->currentform];					// Auxiliary variable
				if($form->nselcontrol < 0) {
					$wb->mousecpos = NOWHERE;
					break;
				}

				$ct = $wb->form[$wb->currentform]->ct[$form->nselcontrol];	// Aux variable

				//-------------- Mouse position tests

				// Is mouse cursor inside the control area?

				if(($xleft >= $ct->left) && ($xleft <= $ct->left + $ct->width) &&
				   ($ytop >= $ct->top) && ($ytop <= $ct->top + $ct->height)) {
					SetMyCursor(IDC_HAND);
					$wb->mousecpos = INCONTROL;
					break;

				// Test for North handle

				} else if(
				  ($xleft >= $ct->left + ($ct->width - HANDLE_WIDTH) / 2) &&
				  ($xleft <= $ct->left + ($ct->width + HANDLE_WIDTH) / 2) &&
				  ($ytop  >= $ct->top  - HANDLE_YOFFSET) &&
				  ($ytop  <= $ct->top  - HANDLE_YOFFSET + HANDLE_HEIGHT)) {
					SetMyCursor(IDC_SIZENS);
					$wb->mousecpos = INNHANDLE;
					break;

				// Test for East handle

				} else if(
				  ($xleft >= $ct->left + $ct->width + HANDLE_XOFFSET - HANDLE_WIDTH) &&
				  ($xleft <= $ct->left + $ct->width + HANDLE_XOFFSET) &&
				  ($ytop  >= $ct->top  + ($ct->height - HANDLE_HEIGHT) / 2) &&
				  ($ytop  <= $ct->top  + ($ct->height + HANDLE_HEIGHT) / 2)) {
					SetMyCursor(IDC_SIZEWE);
					$wb->mousecpos = INEHANDLE;
					break;

				// Test for West handle

				} else if(
				  ($xleft >= $ct->left - HANDLE_XOFFSET) &&
				  ($xleft <= $ct->left - HANDLE_XOFFSET + HANDLE_WIDTH) &&
				  ($ytop  >= $ct->top  + ($ct->height - HANDLE_HEIGHT) / 2) &&
				  ($ytop  <= $ct->top  + ($ct->height + HANDLE_HEIGHT) / 2)) {
					SetMyCursor(IDC_SIZEWE);
					$wb->mousecpos = INWHANDLE;
					break;

				// Test for South handle

				} else if(
				  ($xleft >= $ct->left + ($ct->width - HANDLE_WIDTH) / 2) &&
				  ($xleft <= $ct->left + ($ct->width + HANDLE_WIDTH) / 2) &&
				  ($ytop  >= $ct->top  + $ct->height + HANDLE_YOFFSET - HANDLE_HEIGHT) &&
				  ($ytop  <= $ct->top  + $ct->height + HANDLE_YOFFSET)) {
					SetMyCursor(IDC_SIZENS);
					$wb->mousecpos = INSHANDLE;
					break;

				// Test for Northeast handle

				} else if(
				  ($xleft >= $ct->left + $ct->width + HANDLE_XOFFSET - HANDLE_WIDTH) &&
				  ($xleft <= $ct->left + $ct->width + HANDLE_XOFFSET) &&
				  ($ytop  >= $ct->top  - HANDLE_YOFFSET) &&
				  ($ytop  <= $ct->top  - HANDLE_YOFFSET + HANDLE_HEIGHT)) {
					SetMyCursor(IDC_SIZENESW);
					$wb->mousecpos = INNEHANDLE;
					break;

				// Test for Shoutheast handle

				} else if(
				  ($xleft >= $ct->left + $ct->width + HANDLE_XOFFSET - HANDLE_WIDTH) &&
				  ($xleft <= $ct->left + $ct->width + HANDLE_XOFFSET) &&
				  ($ytop  >= $ct->top  + $ct->height + HANDLE_YOFFSET - HANDLE_HEIGHT) &&
				  ($ytop  <= $ct->top  + $ct->height + HANDLE_YOFFSET)) {
					SetMyCursor(IDC_SIZENWSE);
					$wb->mousecpos = INSEHANDLE;
					break;

				// Test for Northwest handle

				} else if(
				  ($xleft >= $ct->left - HANDLE_XOFFSET) &&
				  ($xleft <= $ct->left - HANDLE_XOFFSET + HANDLE_WIDTH) &&
				  ($ytop  >= $ct->top  - HANDLE_YOFFSET) &&
				  ($ytop  <= $ct->top  - HANDLE_YOFFSET + HANDLE_HEIGHT)) {
					SetMyCursor(IDC_SIZENWSE);
					$wb->mousecpos = INNWHANDLE;
					break;

				// Test for Shouthwest handle

				} else if(
				  ($xleft >= $ct->left - HANDLE_XOFFSET) &&
				  ($xleft <= $ct->left - HANDLE_XOFFSET + HANDLE_WIDTH) &&
				  ($ytop  >= $ct->top  + $ct->height + HANDLE_YOFFSET - HANDLE_HEIGHT) &&
				  ($ytop  <= $ct->top  + $ct->height + HANDLE_YOFFSET)) {
					SetMyCursor(IDC_SIZENESW);
					$wb->mousecpos = INSWHANDLE;
					break;
				} else
					$wb->mousecpos = NOWHERE;

			}

			break;

		case IDCLOSE:
			end_drawing_functions();
			wb_destroy_window($window);
			break;
	}
}

//------------------------------------------------------------------ END OF FILE

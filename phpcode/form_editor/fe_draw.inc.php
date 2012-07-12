<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - Drawing functions

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function start_drawing_functions()
{
	global $cl;

	$cl = new Wb;

	// Initialize colors

	$cl->titlebar = 0x674F3C;
	$cl->hilite = WHITE;
	$cl->face = 0xD8DCDC;
	$cl->edit = WHITE;
	$cl->disabled = 0xD6D8D8;
	$cl->editshade = 0xA0A0A0;
	$cl->shade = 0x788080;
	$cl->shadow = BLACK;
	$cl->window = 0xE4E2E2;
	$cl->gridcolor = 0xC8D8D8;
	$cl->outline = 0xFCB0B0;
	$cl->gauge = 0x804040;
	$cl->handle = DARKBLUE;
	$cl->cbreadonly = 0xEAEFEF;

	// Initialize fonts

	$cl->sysfont = wb_create_font("Tahoma", 8, BLACK);
	$cl->hyperlink = wb_create_font("Tahoma", 8, 0xC00000, FTA_UNDERLINE);
	$cl->htmlfont = wb_create_font("Verdana", 10, BLUE, FTA_UNDERLINE);
	$cl->rtffont = wb_create_font("Tahoma", 8, RED, FTA_BOLD);
	$cl->disabledfont = wb_create_font("Tahoma", 8, 0xA0A0A0);
	$cl->hilitefont = wb_create_font("Tahoma", 8, $cl->hilite);
	$cl->ghostfont = wb_create_font("Tahoma", 8, 0x788080);
	$cl->winfont = wb_create_font("Tahoma", 8, WHITE, FTA_BOLD);

}

function end_drawing_functions()
{
	global $cl;

	// Destroy fonts

	wb_destroy_font($cl->sysfont);
	wb_destroy_font($cl->winfont);

	// Destroy images

	wb_destroy_image($cl->winicon);
}

function draw_background($buffer, $width, $height)
{
	global $cl, $wb;

	wb_draw_rect($buffer, 0, 0, $width, $height, $cl->window, true);

	// Draw the grid

	if(!$wb->wireframe && $wb->showgrid) {
		for($y = 0; $y < $height; $y += 10)
			wb_draw_line($buffer, 0, $y, $width, $y, $cl->gridcolor);
		for($x = 0; $x < $width; $x += 10)
			wb_draw_line($buffer, $x, 0, $x, $height, $cl->gridcolor);
	}
}

function draw_ghost($buffer, $old)
{
	global $wb, $cl;

	$wb->ghoststate = true;
	$sysfont = $cl->sysfont;
	$cl->sysfont = $cl->ghostfont;
	$wb->wirestyle = 1;
	call_user_func("draw_" . strtolower($old->cclass), $buffer, $old->caption, $old->left, $old->top, $old->width, $old->height, $old->style, $old->value);
	$wb->wirestyle = 0;
	$cl->sysfont = $sysfont;
	$wb->ghoststate = false;
}

function draw_handles($buffer, $caption, $xstart, $ystart, $width, $height)
{
	global $wb, $cl;

	$xcen = $xstart + $width / 2;
	$xend = $xstart + $width;
	$ymid = $ystart + $height / 2;
	$yend = $ystart + $height;

	// Draw top selection handles

	wb_draw_rect($buffer, $xstart - HANDLE_XOFFSET, $ystart - HANDLE_YOFFSET,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);
	wb_draw_rect($buffer, $xcen - HANDLE_WIDTH / 2, $ystart - HANDLE_YOFFSET,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);
	wb_draw_rect($buffer, $xend + HANDLE_XOFFSET - HANDLE_WIDTH, $ystart - HANDLE_YOFFSET,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);

	// Draw middle selection handles

	wb_draw_rect($buffer, $xstart - HANDLE_XOFFSET, $ymid - HANDLE_HEIGHT / 2,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);
	wb_draw_rect($buffer, $xend + HANDLE_XOFFSET - HANDLE_WIDTH, $ymid - HANDLE_HEIGHT / 2,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);

	// Draw bottom selection handles

	wb_draw_rect($buffer, $xstart - HANDLE_XOFFSET, $yend + HANDLE_YOFFSET - HANDLE_HEIGHT,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);
	wb_draw_rect($buffer, $xcen - HANDLE_WIDTH / 2, $yend + HANDLE_YOFFSET - HANDLE_HEIGHT,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);
	wb_draw_rect($buffer, $xend + HANDLE_XOFFSET - HANDLE_WIDTH, $yend + HANDLE_YOFFSET - HANDLE_HEIGHT,
		HANDLE_WIDTH, HANDLE_HEIGHT, $cl->handle, true);

	// Draw outline (dashed)

	wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->outline, false, 0, 1);
}

//--------------------------------------------------- FUNCTIONS TO DRAW CONTROLS

/* This function calls the function according to the class of control $ct */

function draw_control($buffer, $ct)
{
	call_user_func("draw_" . strtolower($ct->cclass), $buffer, $ct->caption, $ct->left, $ct->top, $ct->width, $ct->height, $ct->style, $ct->value);
}

function draw_label($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	wb_draw_text($buffer, $caption, $xstart, $ystart, $width, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, $style);
}

function draw_hyperlink($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	if($style & WBC_DISABLED)
		$font = $cl->disabledfont;
	else if($wb->wireframe)
		$font = $cl->sysfont;
	else
		$font = wb_create_font("Tahoma", 8, $value, $style & WBC_LINES ? FTA_UNDERLINE : 0);

	wb_draw_text($buffer, $caption, $xstart, $ystart, $width, $height,
		$font, ($style & WBC_CENTER ? WBC_CENTER : WBC_LEFT) | WBC_TOP);

	if(!($style & WBC_DISABLED) && !$wb->wireframe)
		wb_destroy_font($font);
}

function draw_invisiblearea($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->outline, false, 0, 3);
}

function draw_imagebutton($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0)
{
	global $cl, $wb;

	// Draw image

	if(!$wb->wireframe && !$wb->ghoststate) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		if((substr($caption, -4) == '.bmp') && file_exists($caption))
			$image = wb_load_image($caption);
		else
			$image = wb_load_image(PATH_RESPVT . 'symb_imagebutton.bmp');

		for($y = 0; $y < $height; $y += 32) {
			for($x = 0; $x < $width; $x += 32) {
				wb_draw_image($buffer, $image,
					$xstart + $x,
					$ystart + $y,
					min(32, $width - $x),
					min(32, $height - $y)
				);
			}
		}

		wb_destroy_image($image);
	}
}

function draw_pushbutton($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0)
{
	global $cl, $wb;

	// Draw button face

	$xend = $xstart + $width;
	$yend = $ystart + $height;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->shade, false, 1, $wb->wirestyle);
		wb_draw_text($buffer, $caption, $xstart, $ystart, $width, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_CENTER | WBC_MIDDLE);
	} else {
		if($style & WBC_DISABLED) {
			wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->face, true);
			wb_draw_line($buffer, $xend - 2, $ystart, $xend - 2, $yend - 2, $cl->face);
			wb_draw_line($buffer, $xstart, $yend - 2, $xend - 2, $yend - 2, $cl->face);
			wb_draw_line($buffer, $xstart, $ystart, $xend - 2, $ystart, $cl->hilite);
			wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 2, $cl->hilite);
			wb_draw_line($buffer, $xend - 1, $ystart, $xend - 1, $yend - 1, $cl->shade);
			wb_draw_line($buffer, $xstart, $yend - 1, $xend - 2, $yend - 1, $cl->shade);
			wb_draw_text($buffer, $caption, $xstart + 1, $ystart + 1, $width, $height, $cl->hilitefont, WBC_CENTER | WBC_MIDDLE);
			wb_draw_text($buffer, $caption, $xstart, $ystart, $width, $height, $cl->disabledfont, WBC_CENTER | WBC_MIDDLE);
		} else {
			wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->face, true);
			wb_draw_line($buffer, $xend - 2, $ystart, $xend - 2, $yend - 2, $cl->shade);
			wb_draw_line($buffer, $xstart, $yend - 2, $xend - 2, $yend - 2, $cl->shade);
			wb_draw_line($buffer, $xstart, $ystart, $xend - 2, $ystart, $cl->hilite);
			wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 2, $cl->hilite);
			wb_draw_line($buffer, $xend - 1, $ystart, $xend - 1, $yend - 1, $cl->shadow);
			wb_draw_line($buffer, $xstart, $yend - 1, $xend - 2, $yend - 1, $cl->shadow);
			wb_draw_text($buffer, $caption, $xstart, $ystart, $width, $height, $cl->sysfont, WBC_CENTER | WBC_MIDDLE);
		}
	}
}

function draw_spinner($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	// Draw buttons

	$btnheight = $height / 2;

	draw_pushbutton($buffer, " ", $xstart, $ystart, $width, $btnheight);
	draw_pushbutton($buffer, " ", $xstart, $ystart + $btnheight, $width, $btnheight);

	// Draw arrows

	if($width > 7 && $height > 11 && (!$wb->wireframe && !$wb->ghoststate)) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$uparrow = wb_load_image(PATH_RESPVT . "symb_uparrow.bmp");
		wb_draw_image($buffer, $uparrow,
			$xstart + ($width - 7) / 2,
			$ystart + $btnheight / 2 - 3,
			7, 4, GREEN);
		wb_destroy_image($uparrow);

		$dnarrow = wb_load_image(PATH_RESPVT . "symb_dnarrow.bmp");
		wb_draw_image($buffer, $dnarrow,
			$xstart + ($width - 7) / 2,
			$ystart + $height - $btnheight + $btnheight / 2 - 2,
			7, 4, GREEN);
		wb_destroy_image($dnarrow);
	}
}

function draw_scrollbar($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->shade, false, 1, $wb->wirestyle);
	} else {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->disabled, true);
	}

	if($height < $width) {
		draw_horiz_scrollbar($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value);
		return;
	}

	// Draw buttons

	$btnheight = min(18, $height / 2);

	draw_pushbutton($buffer, " ", $xstart, $ystart, $width, $btnheight);
	draw_pushbutton($buffer, " ", $xstart, $ystart + $btnheight, $width,
		min(24, $height - ($btnheight * 2)));
	draw_pushbutton($buffer, " ", $xstart, $ystart + $height - $btnheight, $width, $btnheight);

	// Draw arrows

	if($width > 7 && $height > 11 && (!$wb->wireframe && !$wb->ghoststate)) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$uparrow = wb_load_image(PATH_RESPVT . "symb_uparrow.bmp");
		wb_draw_image($buffer, $uparrow,
			$xstart + ($width - 7) / 2,
			$ystart + $btnheight / 2 - 3,
			7, 4, GREEN);
		wb_destroy_image($uparrow);

		$dnarrow = wb_load_image(PATH_RESPVT . "symb_dnarrow.bmp");
		wb_draw_image($buffer, $dnarrow,
			$xstart + ($width - 7) / 2,
			$ystart + $height - $btnheight + $btnheight / 2 - 2,
			7, 4, GREEN);
		wb_destroy_image($dnarrow);
	}
}

function draw_horiz_scrollbar($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	// Draw buttons

	$btnwidth = min(18, $width / 2);

	draw_pushbutton($buffer, " ", $xstart, $ystart, $btnwidth, $height);
	draw_pushbutton($buffer, " ", $xstart + $btnwidth, $ystart, min(24, $width - ($btnwidth * 2)), $height);
	draw_pushbutton($buffer, " ", $xstart + $width - $btnwidth, $ystart, $btnwidth, $height);

	// Draw arrows

	if($height > 7 && $width > 11 && (!$wb->wireframe && !$wb->ghoststate)) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$lfarrow = wb_load_image(PATH_RESPVT . "symb_lfarrow.bmp");
		wb_draw_image($buffer, $lfarrow,
			$xstart + $btnwidth / 2 - 3,
			$ystart + ($height - 7) / 2,
			4, 7, GREEN);
		wb_destroy_image($lfarrow);

		$rtarrow = wb_load_image(PATH_RESPVT . "symb_rtarrow.bmp");
		wb_draw_image($buffer, $rtarrow,
			$xstart + $width - $btnwidth + $btnwidth / 2 - 3,
			$ystart + ($height - 7) / 2,
			4, 7, GREEN);
		wb_destroy_image($rtarrow);
	}
}

function draw_combobox($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	// Draw outline

	if($wb->constructionlines)
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->outline, false, 0, 3);
	$title = explode(',', $caption);
	draw_editbox($buffer, $title[0], $xstart, $ystart, $width, 20, $style, $value, null,
		$style & WBC_READONLY ? $cl->cbreadonly : NOCOLOR);
	draw_pushbutton($buffer, " ", $xstart + $width - 19, $ystart + 1, 18, 18);

	// Draw arrow

	if($width > 7 && $height > 11 && (!$wb->wireframe && !$wb->ghoststate)) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$dnarrow = wb_load_image(PATH_RESPVT . "symb_dnarrow.bmp");
		wb_draw_image($buffer, $dnarrow,
			$xstart + $width - 19 + 6,
			$ystart + 8,
			7, 4, GREEN);
		wb_destroy_image($dnarrow);
	}
}

function draw_listbox($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	draw_editbox($buffer, $caption, $xstart, $ystart, $width, $height, $style | WBC_MULTILINE, $value);
}

function draw_calendar($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0)
{
	global $cl, $wb;

	if($wb->wireframe || $wb->ghoststate) {
		draw_pushbutton($buffer, $caption, $xstart, $ystart, $width, $height, $style);
	} else {
		if($style & WBC_BORDER)
			draw_sunkenframe($buffer, "", $xstart, $ystart, $width, $height, $style, $value);
		else
			wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $style & WBC_DISABLED ? $cl->disabled : $cl->edit, true);
	}

	// Draw image

	if($width > 7 && $height > 11 && (!$wb->wireframe && !$wb->ghoststate)) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$image = wb_load_image(PATH_RESPVT . "symb_calendar.bmp");
		wb_draw_image($buffer, $image,
			$xstart + max(0, ($width - 197) / 2) + 1,
			$ystart + max(0, ($height - 157) / 2) + 1,
			$width - 2,
			$height - 2);
		wb_destroy_image($image);
	}
}

function draw_gauge($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0)
{
	global $cl, $wb;

	draw_sunkenframe($buffer, $caption, $xstart, $ystart, $width, $height, WBC_DISABLED, $value);

	// Draw rectangular segments

	$color = ($style & WBC_DISABLED) ? $cl->editshade : ($wb->wireframe ? $cl->sysfont : $cl->gauge);

	$bar = min($width - 5, max(0, (int)((float)(($height - 6) * 3) / 4)));
	$xend = max(1, ($width * $value / 100) - ($bar + 2));
	if($bar) {
		for($xoff = 0; $xoff < $xend; $xoff += $bar + 2) {
			wb_draw_rect($buffer, $xoff + $xstart + 2, $ystart + 2, $bar, $height - 5,
				$color, !($wb->wireframe || $wb->ghoststate));
		}
	}
}

function draw_slider($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0)
{
	global $cl, $wb;

	$curheight = max(1, min(20, $height - 6));

	draw_sunkenframe($buffer, $caption, $xstart + 10,
		$ystart + ($curheight - 2) / 2 + 3,
		$width - 20, 3, WBC_DISABLED, $value);

	// Draw cursor

	$curwidth = min(10, $curheight * 5 / 9);
	$xoff = min($width - $curwidth, max(0, ($width - $curwidth) * $value / 100));
	draw_pushbutton($buffer, "", $xstart + $xoff, $ystart + 3, $curwidth, $curheight);
}

function draw_rtfeditbox($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	draw_editbox($buffer, $caption, $xstart, $ystart, $width, $height, $style | WBC_MULTILINE, $value,
		$wb->wireframe ? $cl->sysfont : $cl->rtffont);
}

function draw_editbox($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0, $font=null, $color=NOCOLOR)
{
	global $cl, $wb;

	draw_sunkenframe($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value, $font, $color);

	// Draw text

	if($style & WBC_MASKED) {
		$caption = str_repeat("*", strlen($caption));
		$ystart += 3;
	}

	if(strstr($caption, ','))
		$title = str_replace(',', "\n", $caption);
	else
		$title = str_repeat($caption . "\r\n", 20);

	if($style & WBC_MULTILINE) {
		wb_draw_text($buffer, $title, $xstart + 4, $ystart + 2, $width - 4, $height - 2,
		  (($style & WBC_DISABLED) ? $cl->disabledfont : ($font ? $font : $cl->sysfont)),
		  WBC_MULTILINE | $style);
		draw_scrollbar($buffer, $caption, $xstart + $width - 19, $ystart + 1, 18, $height - 2, $style, $value);
	} else {
		wb_draw_text($buffer, $caption, $xstart + 4, $ystart + 2, $width - 4, $height - 2,
		  (($style & WBC_DISABLED) ? $cl->disabledfont : ($font ? $font : $cl->sysfont)),
		  WBC_LEFT | WBC_TOP | $style);
	}
}

function draw_checkbox($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;
	static $unchecked = null, $checked = null;

	// Draw background

	$height = 13;
	$xend = $xstart + $width;
	$yend = $ystart + $height;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart, 12, 12, $cl->shade, false, 1, $wb->wirestyle);
	} else {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$image = wb_load_image(PATH_RESPVT . ($value ? "draw_checkbox_checked.bmp" : "draw_checkbox.bmp"));
		wb_draw_image($buffer, $image, $xstart, $ystart);
		wb_destroy_image($image);
	}

	// Draw checkbox text

	wb_draw_text($buffer, $caption, $xstart + 16, $ystart, $width - 16, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_LEFT | WBC_MIDDLE);
}

function draw_radiobutton($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;
	static $unchecked = null, $checked = null;

	// Draw background

	$height = 13;
	$xend = $xstart + $width;
	$yend = $ystart + $height;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_ellipse($buffer, $xstart + 1, $ystart + 1, 11, 11, $cl->shade, false, 1, $wb->wirestyle);
	} else {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$image = wb_load_image(PATH_RESPVT . ($value ? "draw_radio_sel.bmp" : "draw_radio.bmp"));
		wb_draw_image($buffer, $image, $xstart, $ystart);
		wb_destroy_image($image);
	}

	// Draw checkbox text

	wb_draw_text($buffer, $caption, $xstart + 16, $ystart, $width - 16, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_LEFT | WBC_MIDDLE);
}

function draw_tabcontrol($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	$xend = $xstart + $width;
	$yend = $ystart + $height;

	// Draw tabs

	$xtab = 0;
	if($caption) {
		$tabtext = preg_split("/,/", $caption, -1, PREG_SPLIT_NO_EMPTY);
		$ntabs = count($tabtext);
		$tabheight = 20;
		for($i = 0; $i < $ntabs; $i++) {
			$xtab += draw_tab($buffer, $tabtext[$i], $xstart + $xtab, $ystart, $tabheight, $style);
			if($i == 0)
				$xtab1 = $xtab - 2;
		}
	} else {
		$tabheight = 0;
		$tabwidth = 0;
	}

	// Draw body

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart + $tabheight - 1, $width, $height - $tabheight - 1, $cl->shade, false, 1, $wb->wirestyle);
	} else {
		wb_draw_rect($buffer, $xstart, $ystart + $tabheight - 1, $width, $height - $tabheight - 1, $cl->face, true);
		wb_draw_line($buffer, $xend - 2, $ystart + $tabheight - 1, $xend - 2, $yend - 2, $cl->shade);
		wb_draw_line($buffer, $xstart, $yend - 2, $xend - 2, $yend - 2, $cl->shade);
		wb_draw_line($buffer, $xstart + $xtab1, $ystart + $tabheight - 1, $xend - 2, $ystart + $tabheight - 1, $cl->hilite);
		wb_draw_line($buffer, $xstart, $ystart + $tabheight - 1, $xstart, $yend - 2, $cl->hilite);
		wb_draw_line($buffer, $xend - 1, $ystart + $tabheight - 1, $xend - 1, $yend - 1, $cl->shadow);
		wb_draw_line($buffer, $xstart, $yend - 1, $xend - 2, $yend - 1, $cl->shadow);
	}
}

function draw_tab($buffer, $caption, $xstart, $ystart, $height, $style)
{
	global $cl, $wb;

	$size = wb_get_size($caption);
	$width = max(40, $size[0] + 16);

	$xend = $xstart + $width;
	$yend = $ystart + $height;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->shade, false, 1, $wb->wirestyle);
	} else {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->face, true);
		wb_draw_line($buffer, $xend - 2, $ystart, $xend - 2, $yend - 2, $cl->shade);
		wb_draw_line($buffer, $xstart, $ystart, $xend - 2, $ystart, $cl->hilite);
		wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 2, $cl->hilite);
	}

	// Draw tab text

	wb_draw_text($buffer, $caption, $xstart + 8, $ystart, $width, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_LEFT | WBC_MIDDLE);
	return $width;
}

function draw_listview($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl;

	draw_editbox($buffer, "", $xstart, $ystart, $width, $height);
	draw_scrollbar($buffer, $caption, $xstart + $width - 19, $ystart + 1, 18, $height - 2, $style, $value);

	$lwidth = $width - 18;
	$title = explode(',', $caption);
	$nhead = count($title);
	$xpos = $xstart + 1;

	// Draw header titles

	for($i = 0; $i < $nhead; $i++) {
		if($xpos < $lwidth - 36) {
			$size = wb_get_size($title[$i], null);
			$width = $size[0] + 20;
			draw_pushbutton($buffer, " ", $xpos, $ystart + 1, $width, 16);
			wb_draw_text($buffer, $title[$i], $xpos, $ystart + 1, $width, 16, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_CENTER | WBC_MIDDLE);
			$xpos += $width;
			if($style & WBC_LINES) {
				wb_draw_line($buffer, $xpos, $ystart, $xpos, $ystart + $height, $cl->face);
			}
		}
	}

	// Draw last (blank) title if needed

	$width = $lwidth - $xpos + 18;
	if($width > 0) {
		draw_pushbutton($buffer, " ", $xpos, $ystart + 1, $width, 16);
	}

	// Draw horizontal lines

	if($style & WBC_LINES)
		for($y = 30; $y < $height; $y += 13) {
			wb_draw_line($buffer, $xstart + 2, $ystart + $y, $xstart + $lwidth, $ystart + $y, $cl->face);
		}
}

function draw_treeview($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	if($wb->wireframe || $wb->ghoststate)
		draw_pushbutton($buffer, $caption, $xstart, $ystart, $width, $height, $style);
	else
		draw_editbox($buffer, '', $xstart, $ystart, $width, $height, $style, $value);
	draw_scrollbar($buffer, $caption, $xstart + $width - 19, $ystart + 1, 18, $height - 2, $style, $value);

	// Draw image

	if(!$wb->wireframe && !$wb->ghoststate) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		if($style & WBC_LINES)
			$image = wb_load_image(PATH_RESPVT . "draw_treeview_lines.bmp");
		else
			$image = wb_load_image(PATH_RESPVT . "draw_treeview.bmp");
		wb_draw_image($buffer, $image,
			$xstart + 2,
			$ystart + 2,
			$width - 20,
			$height - 4);
		wb_destroy_image($image);
	}
}

function draw_htmlcontrol($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $wb, $cl;

	if($wb->wireframe || $wb->ghoststate) {
		draw_pushbutton($buffer, $caption, $xstart, $ystart, $width, $height, $style);
	} else {
		if($style & WBC_BORDER)
			draw_sunkenframe($buffer, "", $xstart, $ystart, $width, $height, $style, $value);
		else
			wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $style & WBC_DISABLED ? $cl->disabled : $cl->edit, true);
	}

	// Draw image

	if(!$wb->wireframe && !$wb->ghoststate) {

		// Bitmaps are loaded right here because initializing these bitmaps
		// in another place (like start_drawing_functions()) or as static
		// variables generates black bitmaps because of some bug in WinBinder!

		$image = wb_load_image(PATH_RESPVT . "draw_htmlcontrol.bmp");
		wb_draw_image($buffer, $image,
			$xstart + 2,
			$ystart + 2,
			$width - 20,
			$height - 4);
		wb_destroy_image($image);
	}

	draw_scrollbar($buffer, $caption, $xstart + $width - 19, $ystart + 1, 18, $height - 2, $style, $value);
}

// Draw a GroupBox

function draw_frame($buffer, $caption, $xstart, $ystart, $width, $height, $style, $value)
{
	global $cl, $wb;

	if(($width > $height) && $height <= 8) {

		// Draw a horizontal line

		$xend = $xstart + $width;

		if($wb->wireframe || $wb->ghoststate) {
			wb_draw_line($buffer, $xstart, $ystart + 0, $xend - 1, $ystart + 0, $cl->shade, 0, $wb->wirestyle);
		} else {
			wb_draw_line($buffer, $xstart, $ystart + 1, $xend - 1, $ystart + 1, $cl->hilite);
			wb_draw_line($buffer, $xstart, $ystart + 0, $xend - 1, $ystart + 0, $cl->shade);
		}

	} else if(($width < $height) && $width <= 8) {

		// Draw a vertical line

		$yend = $ystart + $height;

		if($wb->wireframe || $wb->ghoststate) {
			wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 1, $cl->shade, 0, $wb->wirestyle);
		} else {
			wb_draw_line($buffer, $xstart + 1, $ystart, $xstart + 1, $yend - 1, $cl->hilite);
			wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 1, $cl->shade);
		}

	} else {

		// Draw the frame box

		$xend = $xstart + $width;
		$yend = $ystart + $height;

		if($width <= 14)
			$caption = "";
		else
			$textsize = wb_get_size($caption, $cl->sysfont);

		if($wb->wireframe || $wb->ghoststate) {

			if($caption) {
				wb_draw_line($buffer, $xstart, $ystart + 6, $xstart + 10, $ystart + 6, $cl->shade, 0, $wb->wirestyle);
				if($width > 14 + $textsize[0]) {
					wb_draw_line($buffer, $xstart + 17 + $textsize[0], $ystart + 6, $xend - 1, $ystart + 6, $cl->shade, 0, $wb->wirestyle);
				}
			} else {
				wb_draw_line($buffer, $xstart, $ystart + 6, $xend - 1, $ystart + 6, $cl->shade, 0, $wb->wirestyle);
			}
			wb_draw_line($buffer, $xstart, $ystart + 6, $xstart, $yend - 1, $cl->shade, 0, $wb->wirestyle);
			wb_draw_line($buffer, $xend - 1, $ystart + 6, $xend - 1, $yend - 1, $cl->shade, 0, $wb->wirestyle);
			wb_draw_line($buffer, $xstart, $yend - 1, $xend - 1, $yend - 1, $cl->shade, 0, $wb->wirestyle);

		} else {

			if($caption) {
				wb_draw_line($buffer, $xstart + 1, $ystart + 7, $xstart + 10, $ystart + 7, $cl->hilite);
				if($width > 14 + $textsize[0]) {
					wb_draw_line($buffer, $xstart + 17 + $textsize[0], $ystart + 7, $xend, $ystart + 7, $cl->hilite);
				}
			} else {
				wb_draw_line($buffer, $xstart + 1, $ystart + 7, $xend - 1, $ystart + 7, $cl->hilite);
			}
			wb_draw_line($buffer, $xstart + 1, $ystart + 7, $xstart + 1, $yend, $cl->hilite);
			wb_draw_line($buffer, $xend, $ystart + 7, $xend, $yend, $cl->hilite);
			wb_draw_line($buffer, $xstart + 1, $yend, $xend, $yend, $cl->hilite);

			if($caption) {
				wb_draw_line($buffer, $xstart, $ystart + 6, $xstart + 10, $ystart + 6, $cl->shade);
				if($width > 14 + $textsize[0]) {
					wb_draw_line($buffer, $xstart + 17 + $textsize[0], $ystart + 6, $xend - 1, $ystart + 6, $cl->shade);
				}
			} else {
				wb_draw_line($buffer, $xstart, $ystart + 6, $xend - 1, $ystart + 6, $cl->shade);
			}
			wb_draw_line($buffer, $xstart, $ystart + 6, $xstart, $yend - 1, $cl->shade);
			wb_draw_line($buffer, $xend - 1, $ystart + 6, $xend - 1, $yend - 1, $cl->shade);
			wb_draw_line($buffer, $xstart, $yend - 1, $xend - 1, $yend - 1, $cl->shade);
		}

		// Draw frame text

		wb_draw_text($buffer, $caption, $xstart + 14, $ystart, $width - 14, $height, ($style & WBC_DISABLED) ? $cl->disabledfont : $cl->sysfont, WBC_LEFT | WBC_TOP);
	}
}

function draw_sunkenframe($buffer, $caption, $xstart, $ystart, $width, $height, $style=0, $value=0, $font=null, $color=NOCOLOR)
{
	global $cl, $wb;

	// Draw background

	$xend = $xstart + $width;
	$yend = $ystart + $height;

	if($wb->wireframe || $wb->ghoststate) {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $cl->shade, false, 1, $wb->wirestyle);
	} else {
		wb_draw_rect($buffer, $xstart, $ystart, $width, $height, $color != NOCOLOR ? $color : ($style & (WBC_DISABLED | WBC_READONLY) ? $cl->disabled : $cl->edit), true);
		wb_draw_line($buffer, $xend - 2, $ystart, $xend - 2, $yend - 2, $cl->editshade);
		wb_draw_line($buffer, $xstart, $yend - 2, $xend - 2, $yend - 2, $cl->editshade);
		wb_draw_line($buffer, $xstart, $ystart, $xend - 2, $ystart, $cl->shadow);
		wb_draw_line($buffer, $xstart, $ystart, $xstart, $yend - 2, $cl->shadow);
	}
}

//------------------------------------------------------------------ END OF FILE

<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ToDo: import/export functions

*******************************************************************************/

//-------------------------------------------------------------------- CONSTANTS

define("COMPLETENAME",	"WinBinder To Do / Bug list");

//--------------------------------------------------------------- MAIN FUNCTIONS

function export_txt($window)
{
	global $statusbar;

	$filename = "todo_items.txt";
	$filename = wb_sys_dlg_save($window, "Export text",
		"Text files (*.txt)\0*.txt\0All files (*.*)\0*.*" . "\0\0",
		null, $filename);
	if(!$filename)
		return;

	$data = db_get_data("item", null, "");
	$txt = todo_export_text($data);

	file_put_contents($filename, $txt);
	wb_set_text($statusbar, basename($filename) . " exported successfully.");
}

function export_html($window=null)
{
	global $statusbar;

	$filename = "todo_items.html";
	$filename = wb_sys_dlg_save($window, "Export HTML",
		"HTML files (*.html, *.htm)\0*.html;*.htm\0All files (*.*)\0*.*" . "\0\0",
		null, $filename);
	if(!$filename)
		return;
	$data = db_get_data("item", null, "");
	$html = todo_export_html($data);
	file_put_contents($filename, $html);
	wb_set_text($statusbar, basename($filename) . " exported successfully.");
}

function export_csv($window)
{
	global $statusbar;

	$filename = "todo_items.csv";
	$filename = wb_sys_dlg_save($window, "Export CSV",
		"CSV files (*.csv, *.txt)\0*.csv;*.txt\0All files (*.*)\0*.*" . "\0\0",
		null, $filename);
	if(!$filename)
		return;

	$data = db_get_data("item", null, "");

	$csv = "";
	foreach($data as $entry) {
		$nelem = count($entry);
		for($i = 1; $i < $nelem; $i++) {
			$value = $entry[$i];
			$comma = $i < $nelem - 1 ? "," : "";
			$csv .= is_numeric(trim($value)) ? trim($value) . $comma :
			  "\"" . addcslashes($value, "\0..\31'`\"\r\n\t") . "\"$comma";
		}
		$csv .= "\r\n";
	}

	file_put_contents($filename, $csv);
	wb_set_text($statusbar, basename($filename) . " exported successfully.");
}

// TODO: Must check if escaped characters are imported correctly

function import_csv($window)
{
	global $statusbar;

	$filename = wb_sys_dlg_open($window, "Import CSV",
		"CSV files (*.csv, *.txt)\0*.csv;*.txt\0All files (*.*)\0*.*" . "\0\0");
	if($filename)
		$csv = file($filename);
	else
		return false;

	$n = 0;
	foreach($csv as $line) {
		$line = trim($line);
		if($line) {
			$entry = csv_explode(stripcslashes($line));
			if(edit_record("item", 0, "name\ndescription\ncat\npriority\nseverity", $entry))
				$n++;
		}
	}

	wb_set_text($statusbar, basename($filename) . " imported successfully ($n items added).");
	return true;
}

//--------------------------------------------------------- FORMATTING FUNCTIONS

function todo_export_html($data)
{
	$html = "<!-- Generated automatically by " . APPNAME . " in " . date("m/d/Y") . " -->\r\n\r\n<html>\r\n<title>" . COMPLETENAME .
	  "</title>\r\n<style>body{font-family:Verdana;font-size:10pt}</style>\r\n<body>\r\n<h2>" .
	  COMPLETENAME . "</h2>" . date("F j, Y, g:i a") . "<br>\r\n" . count($data) . " items<hr><br>\r\n\r\n";
	foreach($data as $entry) {
		$id = $entry[0];
		$name = htmlentities(trim($entry[1]));
		$desc = trim($entry[2]);
		if(!$desc)
			$desc= "(No description)";
		else
			$desc = nl2br(htmlentities($desc));
		$cat = db_get_data("cat", null, "name", "id == {$entry[3]}");
		$prior = db_get_data("priority", null, "name", "id == {$entry[4]}");
		$sever = db_get_data("severity", null, "name", "id == {$entry[5]}");

		$html .= "<font size=1 color='#0000A0'>Item/bug #$id</font><br>" .
		  "<font size=4><b>\r\n$name\r\n</b></font><br>" .
		  "<font size=1>($cat) $sever, $prior priority</font><br><br>" .
		  "\r\n$desc<br>\r\n<br><hr><br>\r\n\r\n";
	}
	$html .= "<font size=2>(End of list)</font><br>\r\n\r\n</body></html>";
	return $html;
}

function todo_export_text($data)
{
	$spaces = str_repeat("-", 80);

	$txt = COMPLETENAME . "\r\n" . "Generated automatically by " . APPNAME . " in " . date("m/d/Y") .
	  "\r\n(" . count($data) . " items)\r\n\r\n$spaces\r\n";
	foreach($data as $entry) {
		$id = $entry[0];
		$name = trim($entry[1]);
		$desc = trim($entry[2]);
		if(!$desc)
			$desc= "(No description)";
		$cat = db_get_data("cat", null, "name", "id == {$entry[3]}");
		$prior = db_get_data("priority", null, "name", "id == {$entry[4]}");
		$sever = db_get_data("severity", null, "name", "id == {$entry[5]}");

		$txt .= "\r\nItem/bug #$id: $name\r\n$sever, $prior priority\r\n\r\n$desc\r\n\r\n$spaces\r\n";
	}
	return $txt;
}

//---------------------------------------------------------- AUXILIARY FUNCTIONS

// From http://us2.php.net/explode
// NOTE: Won't work if $str has spaces between commas, like: 12, "Graphics", "Circles", 10
//     a space will be prepended to each string

function csv_explode($str, $delim=',', $qual="\"")
{
	$len = strlen($str);
	$inside = 0;
	$word = '';

	for($i = 0; $i < $len; $i++) {
		if(($str[$i] == $delim) && !$inside) {
			$out[] = $word;
			$word = '';
		} else if($str[$i] == $qual) {
			$inside = !$inside;
		} else {
			$word .= $str[$i];
		}
	}
	$out[] = $word;
	return $out;
}

//------------------------------------------------------------------ END OF FILE

?>

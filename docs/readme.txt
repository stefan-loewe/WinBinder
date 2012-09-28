********************************************************************************

WINBINDER - The native Windows binding for PHP for PHP

Copyright © Hypervisual - see LICENSE.TXT for details
Author: Rubem Pechansky (http://winbinder.org/contact.php)

********************************************************************************



LICENSE INFORMATION
--------------------------------------------------------------------------------

Before using WinBinder you must accept the terms of its license agreement. See
file License.txt for information.

This software uses PHP, the open source general-purpose scripting language
available from http://www.php.net/. See file License-php.txt for information.

This package uses Inno Setup, the free installer for Windows programs. See
License-innosetup.txt for more information.

This package includes the Info-ZIP group's portable UnZip zipfile extraction
program. See License-infozip.txt for more information.

This package includes the FreeImage open source image library. See
http://freeimage.sourceforge.net for details. FreeImage is used under the FIPL,
version 1.00. See file License-freeimage.txt for more information.

The licenses mentioned above are in the /docs folder of this installation.



DISCLAIMER
--------------------------------------------------------------------------------

Although the basic structure of WinBinder is already defined, it is still in
alpha stage. This means that several things are still subject to change,
including specifications, function parameters, parameter order, function names,
and more. It is very likely that you will have to change at least a small part
of your source code in the future. Also note that the documentation is still
incomplete and it is possible that some information within it is not accurate.



WHAT IS IT?
--------------------------------------------------------------------------------

WinBinder is an open source extension to PHP that allows programmers to easily
build native Windows applications.

Go to http://winbinder.org/ for more information and new releases. More
information can be found in the help file included with this release
(winbinder.chm) in the /docs folder.

Enjoy, and feel free to contact me with ideas and suggestions.



SYSTEM REQUIREMENTS
--------------------------------------------------------------------------------

Any desktop system starting from Windows 98 should work, althopugh to get full
functionality you should use Windows 2000, Windows XP or newer.

WinBinder also requires Internet Explorer 5.0 or higher.



AUTOMATIC INSTALLATION
--------------------------------------------------------------------------------

If you are installing WinBinder from an installer (executable file), just follow
the instructions on the setup wizard and ignore the two following steps.



MANUAL WINBINDER INSTALLATION
--------------------------------------------------------------------------------

1) Create a new folder in your hard disk to hold the files, like C:\WinBinder.

2) Unzip all files from the WinBinder archive to the new folder, maintaining the
existing folder structure inside archive.

3) If this is an upgrade or if this package already contains the PHP binaries,
you are done. If this the first time you are installing WinBinder, proceed to
PHP installation below.



MANUAL PHP INSTALLATION
------------------------------------------------------------------------------

To run WinBinder you must install a copy of PHP version 4.3 or 5 in the
WinBinder folder.

The advantage of PHP 4.3 over PHP 5 is a smaller footprint. PHP 5 has about
3.5 MB (1.8 MB zipped) while PHP 4.3 has about 1.4 MB (630 kB zipped). The
advantage of PHP 5 is that it has much better OO support and new functions.

1) If you just want to run WinBinder and do not need PHP for anything else,
you may download a minimal PHP installation for WinBinder at
http://winbinder.org/download.php. If you prefer the full PHP
installation, go to http://www.php.net/downloads.php, find the caption
"Windows Binaries" and download the latest stable version of PHP 4 or 5 as a
zip package (not an installer).

2) Locate the files you will need. For PHP 4, the files are:

	php.exe
	php4ts.dll

For PHP 5 you will need:

	php.exe
	php-win.exe
	php5ts.dll

3) Copy the files to the PHP folder (in the example, C:\Winbinder\binaries\
php4 or C:\WinBinder\binaries\php5) to complete the installation. It does no
harm to have more than one copy of PHP in the same computer, even if versions
are different.

4) In order to run some of the examples you must also copy php_sqlite.dll to
C:\WinBinder\binaries\php4\ext or C:\WinBinder\binaries\php5\ext. Be sure also
to download the FreeImage DLL at http://freeimage.sourceforge.net/. This
library is also available at http://winbinder.org/download.php
under the label "Some additional libraries".

5) If you are running Windows 98 or Me and keep getting "Unable to load dynamic
library" errors, try using a fully qualified path name for the WinBinder
extension. To do this, first locate file php.ini in the \PHP\PHP4 or PHP\PHP5
folder. Look for the "extension_dir" entry in the INI file and change it to the
complete path to your extension folder. In the example above, you would change
it from  "ext/" to "c:/winbinder/binaries/php4/ext/" or
"c:/winbinder/binaries/php5/ext/".



RUNNING WINBINDER SCRIPTS
--------------------------------------------------------------------------------

Once WinBinder and PHP are installed, perform the following steps:

1) In Windows Explorer, associate the .PHPW extension (for WinBinder scripts)
with php-win.exe.

2) There are many code examples in the /PHPCODE/EXAMPLES folder. To see
WinBinder in action, go to that folder and double-click hello.phpw to run it.
You may then take a look at the other scripts available in the other folders.

3) You can also create a tool in your favorite PHP code editor to edit and run
scripts. For PHP code development, it is best to run the console version of PHP
(php.exe) instead of php-win.exe. This way you will be able to see the console
output. Follow the instructions from the text editor, then associate a keyboard
shortcut, e.g. F5, to the new tool. Thus you may open a .phpw file and run it
simply by pressing F5. Look in the /TOOLS folder for specific code editor
information.



CONTACTS AND ADDITIONAL INFORMATION
--------------------------------------------------------------------------------

You will find additional README files in the various folders after installation.

Additional information about WinBinder can be found at:

    http://winbinder.org/

You are welcome to join the WinBinder forum at:

    http://winbinder.org/forum/

Your ideas, comments, criticisms and suggestions are welcome. Please write to:

	Rubem Pechansky
	http://winbinder.org/contact.php
	rubem@winbinder.org

Thanks for your interest in WinBinder!

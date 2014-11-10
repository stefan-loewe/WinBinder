WinBinder
=========

WinBinder - The native Windows binding for PHP

What is WinBinder?
==================

WinBinder is a PHP extension written in C, that allows you to build native 
client-side applications for the Windows Platform in pure PHP.

What is needed to start building WinBinder applications?
========================================================

You will need the source code of PHP plus the WinBinder source code.

On whcih PHP versions has it been tested on?
  - PHP 5.6
  - PHP 5.5 
  - PHP 5.4

How to compile the WinBinder extension?
=======================================

Prerequisites:
--------------
  1. Get Microsoft Visual Studio (Express version will do)
  2. Get PHP source code, e.g. from http://windows.php.net/download/
  3. Compile PHP by following this guide https://wiki.php.net/internals/windows/stepbystepbuild
  4. Clone this repo into the folder ext/winbinder
  5. compile PHP with your options plus --with-winbinder, e.g. configure.bat --enable-snapshot-build --enable-debug-pack --with-winbinder

  Note: This will statically compile it into PHP. Use --with-winbinder=shared to have it in it's own *.dll.

Where to get further information?
=================================

See the docs!

Reporting Bugs
========================

Report them via github issues.

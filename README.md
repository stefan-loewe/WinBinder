
Note
=========
As of 2018-01-01, this repository is shut down, to be restricted to be read-only.

Thanks everybody for their support, esp. @jaredallard!

For an up-to-date development, please consider the fork of user [wagy](https://github.com/wagy/) at [https://github.com/wagy/WinBinder/](https://github.com/wagy/WinBinder/), which reportedly also has PHP 7.X support.

WinBinder
=========

[![Join the chat at https://gitter.im/stefan-loewe/WinBinder](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/stefan-loewe/WinBinder?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

WinBinder - The native Windows binding for PHP

What is WinBinder?
==================

WinBinder is a PHP extension written in C, that allows you to build native 
client-side applications for the Windows Platform in pure PHP.

What is needed to start building WinBinder applications?
========================================================

You will need the source code of PHP plus the WinBinder source code.

On which PHP versions has it been tested on?
  - PHP 5.6
  - PHP 5.5 
  - PHP 5.4

For a release working with PHP 7, please consider the fork of user [wagy](https://github.com/wagy/) at [https://github.com/wagy/WinBinder/](https://github.com/wagy/WinBinder/).

How to compile the WinBinder extension?
=======================================

Prerequisites:
--------------
  1. Get Microsoft Visual Studio (Express version will do)
  2. Get PHP source code, e.g. from http://windows.php.net/download/
  3. Compile PHP by following this guide https://wiki.php.net/internals/windows/stepbystepbuild
  4. Clone this repo into the folder ext/winbinder
  5. compile PHP with your options
    1. add <code>--with-winbinder</code> to compile WinBinder statically into php.exe<br/>
      <code>configure.bat --enable-snapshot-build --enable-debug-pack --with-winbinder</code>
    2. add <code>--with-winbinder=shared</code> to compile WinBinder shared, i.e., to get a *.dll<br/>
      <code>configure.bat --enable-snapshot-build --enable-debug-pack --with-winbinder=shared</code>

Where to get further information?
=================================

See the docs!

Reporting Bugs
========================

Report them via github issues.

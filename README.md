WinBinder
=========

WinBinder - The native Windows binding for PHP

What is WinBinder?
==================

WinBinder is a PHP extension written in C, that allows you to build native 
client-side applications for the Windows Platform in pure PHP.

What is needed to start building WinBinder applications?
========================================================

You will need PHP plus the WinBinder source code.

What has it been tested on?
  - PHP 5.6
  - PHP 5.5 
  - PHP 5.4

How to compile the WinBinder extension?
=======================================

Prerequisites:
--------------
  1. Get Microsoft Visual Studio.
  2. Get PHP source code
  3. Follow this guide to setup PHP https://wiki.php.net/internals/windows/stepbystepbuild
  4. Clone this repo into ext/winbinder
  5. compile PHP with your options plus --with-winbinder

  Note: This will staiclly combine it into PHP. do --with-winbinder=shared to have it in it's own DLL.

Where to get further information?
=================================

Check out the original repository for any weird issues! https://github.com/stefan-loewe/WinBinder

Reporting Bugs
========================

Report them HERE, and if they're a core WinBinder issue I will manage them with the original fork.
However, if it's my problem, I will fix it.

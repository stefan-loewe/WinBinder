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

How to get the WinBinder extension?
===================================

Compile it.

How to compile the WinBinder extension?
=======================================

Prerequisites:
--------------
  1. Get Microsoft Visual Studio 2013
  2. Get PHP source code
  3. Follow this guide to setup PHP https://wiki.php.net/internals/windows/stepbystepbuild
  4. Compile PHP making sure to include the --enable-cli option.
  5. Clone this repo somewhere, you will need to modify where php5ts.lib is in the project.

Creating the Project:
---------------------

  - Likely compile errors (for WinBinder)

    - fatal error C1083: "../main/config.w32.h": No such file or directory
      - open a Visual Studio command prompt and change into you PHP source directory
      - run the command "buildconf.bat"
      - run the command "cscript /nologo configure.js"

    - great, now I get "ERROR: bison is required"
      - either get bison, or open "configure.js" and comment out the following line ...
        ERROR('bison is required')
        ... and re-run "cscript /nologo configure.js"
            
    - You may now try to compile the extension, but you might get ...

    - error C2466: cannot allocate an array of constant size 0
      - go to "C:\path\to\php\source\main" and open "config.w32.h"
      - comment out the following lines  
        \#ifndef \_WIN64  
        \#define _USE\_32BIT\_TIME\_T 1  
        \#endif  
        \#define HAVE\_STDLIB\_H 1
      
    - error C4996: regarding unsafe/deprecated functions
      - again, under "Configuration Properties" select "C/C++", "General" and set SDL checks to "No"

Where to get further information?
=================================

Check out the original repository for any weird issues! https://github.com/stefan-loewe/WinBinder

Reporting Bugs
========================

Report them HERE, and if they're a core WinBinder issue I will manage them with the original fork.
However, if it's my problem, I will fix it.

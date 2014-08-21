WinBinder
=========

WinBinder - The native Windows binding for PHP

What is WinBinder?
==================

WinBinder is a PHP extension written in C, that allows you to build native 
client-side applications for the Windows Platform in pure PHP.

What is needed to start building WinBinder applications?
========================================================

You will need PHP 5.3/5.4/5.5. plus the WinBinder extension matching your PHP version.

How to get the WinBinder extension?
===================================

- use one of the pre-compiled *.dlls in the binaries subfolder of this repository
- if you dare, you may want to compile it yourself

How to compile the WinBinder extension?
=======================================

Prerequisites:
--------------
  1. get a version of Visual Studio (this how-to refers to Visual Studio Express 2012 [Evaluation Version], as this needs to be compiled with VC11, because PHP 5.5. is also compiled with that compiler version)
  2. get the PHP binaries you want to use with WinBinder, e.g. PHP 5.5 VC11 x86 Thread Safe
    - see http://windows.php.net/download/
  3. get the PHP source
    - at http://windows.php.net/download/, see the link "Download source code" matching the version of the binaries from step 2
  4. get the WinBinder source code, which is contained in this repository under "source"

Creating the Project:
---------------------

  - create the project:
    - select "New Project ..."
    - select "Visual C++" and "Win32 Project", and name it "winbinder" for example
    - click "Next"
    - select "DLL" and click "Finish"

  - the editor window should now be opening/opened
  
  - from the menu, select "PROJECT" and "Add Existing Item" and select C:\path\to\WinBinder\source and add all files from that and the "wb" subdirectory

  - again, select "PROJECT" from the menu, and then select "winbinder Properties" to change the following settings

  - change default C++ options:
    - right click your project and select "Properties"
    - under "Configuration Properties" select "General"
        - set "Character Set" to "Use Multi-Byte Character Set"
    
    - under "Configuration Properties" select "C/C++", and there select "General"
        - set "Debug Information Format" to "Program Database (/Zi)""
        - set "Detect 64-bit Portability Issues" to "No"

    - under "Configuration Properties" select "C/C++", and there select "Code Generation"
        - set "Enable String Pooling" to "Yes (/GF)"
        - set "Enable Minimal Rebuild" to "No"
        - set "Basic Runtime Checks" to "Default"
        - set "Runtime Library" to "Multi-threaded Debug (/MTd)"

  - set the include path:
    - again, under "Configuration Properties" select "C/C++", and there select "General"
        - set the include path as following (adapting to your case)
            - C:\path\to\php\source\main
            - C:\path\to\php\source\Zend
            - C:\path\to\php\source\TSRM
            - C:\path\to\php\source\regex (might be missing since PHP 5.5)
            - C:\path\to\php\source
            - C:\path\to\php\source\ext\standard

  - set the Preprocessor Definitions:
    - again, under "Configuration Properties" select "C/C++", and there select "Preprocessor"
        - add the following definitions
            - ZEND_DEBUG=0
            - ZTS=1
            - ZEND_WIN32
            - PHP_WIN32

  - set the Linker Options:
    - under "Configuration Properties" select "Linker", and there select "General"
        - set "Additional Library Directories" to C:\path\to\php\binary\dev

    - under "Configuration Properties" select "Linker", and there select "Input"
        - add "php5.lib" (for non-thred-safe version) or "php5ts.lib" (for thread-safe version) to "Additional Dependecies"

  - edit stdafx.h:
    - replace the contents of stdafx.h with the following, if it does not match already:  
    \#pragma once  
    /* PHP Extension headers */  
    /* include zend win32 config first */  
    \#include "zend_config.w32.h"  
    /* include standard header */  
    \#include "php.h"

  You may now try to compile the extension. When successful, it is located in the folder "Debug" of your Visual Studio project.

  - However, it's more likely, that you run into some errors like:

      - fatal error C1083: "../main/config.w32.h": No such file or directory
          - open a Visual Studio command prompt and change into you PHP source directory
          - run the command "buildconf.bat"
          - run the command "cscript /nologo configure.js"

      - great, now I get "ERROR: bison is required"
          - either get bison, or open "configure.js" and comment out the following line ...
              ERROR('bison is required')
            ... and re-run "cscript /nologo configure.js"
            
      - You may now try to compile the extension, but you migh get ...
              
      - fatal error C1853, C1854 or C2859: something about pre-compiled headers
          - again, under "Configuration Properties" select "C/C++", and there select "Precompiled Headers"
              - set "Use Precompiled Headers" to "Do not use precompiled headers"
              
      - You may now try to compile the extension, but you migh get ...

      - error C2466: cannot allocate an array of constant size 0
          - go to "C:\path\to\php\source\main" and open "config.w32.h"
          - comment out the following lines  
            \#ifndef \_WIN64  
            \#define _USE\_32BIT\_TIME\_T 1  
            \#endif  
            \#define HAVE\_STDLIB\_H 1
      
      - You may now try to compile the extension, but you migh get ...
      
      - error C4996: regarding unsafe/deprecated functions
        - again, under "Configuration Properties" select "C/C++", "General" and set SDL checks to "No"
      
      - You may now try to compile the extension, but you migh get ...

      - error LNK2005: **** already defined in %%%%%
          - delete files automatically generated by Visual Studio from the project, i.e. stdafx.cpp and dllmain.cpp (maybe more?)
          
      - You may now try to compile the extension, but you migh get ...

      - fatal error LNK1000: Internal error during IncrBuildImage
          - update your Visual Studio, LNK1000 may happen due to a bug
          - under "Configuration Properties" select "Linker", and there select "General"
              - set "activate incrementel linking" to "No"

Where to get further information?
=================================

Be sure to check out the docs folder. There are also examples in the phpcode
folder. Futhermore, you can go to www.winbinder.org, there is also a forum.

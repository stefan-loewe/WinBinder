<?php

/**
 * Implements the WinAPI, WindowsLibrary an StaticWindowsLibrary classes
 * REQUIRES: Winbinder
 * 
 * - Example usage: WindowsAPI::library()->function($params);
 * @file
 * @author Andrew Fitzgerald
 * @version 0.8.0 
 */

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
     trigger_error("PHP 5.3+ Reqired for WinAPI", E_USER_ERROR);
}



/**
 * The main class which programmers will access everything else via
 * unless using StaticWindowsLibrary classes
 */
class WinAPI
{  
	/**
	 * \array containing library pointers
	 */
	private static $libraries = array();		

	/**
	 * Returns the specified WindowsLibrary pointer
	 * @param $libraryName \string name of the library or dll to use
	 * @param $args \mixed unused
	 * @return \type{WindowsLibrary} WindowsLibrary object
	 */
   public static function __callStatic($libraryName, $args)
   {
      if (!array_key_exists($libraryName, self::$libraries))
      {  self::$libraries[$libraryName] = new WindowsLibrary($libraryName);
      }

      return self::$libraries[$libraryName];
   } 
   
   public static function libraryIsLoaded($library)
	{
		return array_key_exists($library, self::$libraries);
	}           
}

/**
 * The class which calls the functions for WinAPI
 */
class WindowsLibrary
{  /**
	 * @var $library \int pointer to library
	 * @var $functionPointers \type{\arrayof{\int}}
	 */
	private $library, $functionPointers = array();

	/**
	 * Creates the object.  
	 * @param $libraryName \string Name of library/DLL	 
	 * @exception Throws an exception if library does not exist
	 */
   public function __construct($libraryName)
   {  
   	if (($this->library = wb_load_library($libraryName)) === NULL)
      {	throw new Exception("Library '$libraryName' was not found.");
      }
   }

	/**
	 * Calls the specified function.  
	 * @param $functionName \string name of the function
	 * @param $args \mixed any arguments, usually integers, but strings work most of the time
	 * now also supports \type{cStructs}
	 * @return \int any data returned by the function
	 * @exception Throws an exception if function does not exist
	 */
   public function __call($functionName, $args)
   {  
      if (!array_key_exists($functionName, $this->functionPointers))
      {  $this->functionPointers[$functionName] = wb_get_function_address($functionName, $this->library);

      	if ($this->functionPointers[$functionName] === NULL)
      	{	throw new Exception("Function '$functionName' does not exist in library.");
      	}
      }
      $tempObjects = array();
      
      // check for cStructs
      foreach ($args as $n => $v)
      {	if (is_object($v) && is_subclass_of($v, 'cStruct'))
      	{	$tempObjects[$n] = $v;
      		$args[$n] = $v->getData();
      	}
      }
      
      $ret = wb_call_function($this->functionPointers[$functionName], $args);
      
      // if we found any cStructs, update them
      foreach ($tempObjects as $n => $v)
      {	
      	$tempObjects[$n]->updateData($args[$n]);
      }
      
		return $ret;
   }
}

/**
 * Implements a static class for easier access to functions
 */
class StaticWindowsLibrary
{  
   /**
	 * Calls the specified function via WindowsLibrary::__call() 
	 * @param $functionName \string name of the function
	 * @param $args \mixed any arguments, usually integers, but strings sometimes work
	 * @return \int any data returned by the function
	 * @exception Throws an exception if function does not exist
	 */
   public static function __callStatic($functionName, $args)
   {     
      $libraryName = get_called_class();           
      return WinAPI::$libraryName()->__call($functionName, $args);
   }   
   
   public static function init($lib)
   {	
   	if (WinAPI::libraryIsLoaded($lib))
   	{	
   		throw new \Exception("StaticWindowsLibrary: $lib is already initialized");  
   	}
   	
	   if (eval("class " . $lib . ' extends ' . __CLASS__ . ' { };') !== false)
      {
      	return;
      }
			      
	   throw new \Exception("There was a problem with your type definition.");  
	}     
}

/**
 * Define the common WinAPI libraries
 */
StaticWindowsLibrary::init('Kernel32');
StaticWindowsLibrary::init('Gdi32');
StaticWindowsLibrary::init('User32');
StaticWindowsLibrary::init('Comctl32');
StaticWindowsLibrary::init('Winmm');
StaticWindowsLibrary::init('Comdlg32');
StaticWindowsLibrary::init('Psapi');
StaticWindowsLibrary::init('Shell32');

function wb_get_hwnd($wbWindow)
{
	$hwnd = wb_peek($wbWindow, 4);
	$arr = unpack("Lhwnd", $hwnd);	
	return $arr['hwnd'];
}
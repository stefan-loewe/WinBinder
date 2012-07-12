<?php
namespace cStruct {
/**
 * Implements the cStruct class for use with WinAPI and Winbinder
 *
 * @author Andrew Fitzgerald
 * @version 0.2.2
 *
 * @file
 *
 */


/**
 * Base class for c data types
 *
 */
abstract class cPrimitive
{
   /**
    * \string the format string for pack
    */
   protected static $_packStr;


   /**
    * \int the length in bytes
    */
   protected static $_byteLen;

   /**
    * Returns the pack string format
    * @return \string the format string for use with php's pack() function
    */
   public static function getPackStr($repeatCount = 1)
   {
      $caller = \get_called_class();

      if (isset($caller::$_packStr) && ($caller::$_packStr !== ''))
      {  return $caller::$_packStr  . ($repeatCount > 1 ? $repeatCount : '');
      }

      throw new \Exception("Pack format not defined");

   }

   /**
    * Returns the number of bytes in the primitive
    * @return \int the number of bytes in the primitive
    */
   public static function sizeOf($repeatCount = 1)
   {
      $caller = \get_called_class();

      if (isset($caller::$_byteLen))
      {  return $caller::$_byteLen  * $repeatCount;
      }

      throw new \Exception("Byte length not defined");

   }

   /**
    * Alias of sizeOf()
    */
   public static function getByteLen($repeatCount = 1)
   {
      $caller = \get_called_class();
      return $caller::sizeOf($repeatCount);
   }

   final static function isType($typeName, $type = false)
   {
      if (!$type)
      {
         $type = \get_called_class();
      }
      
		if (is_array(self::checkType($type)))
		{	$type = $type[0];
		}
      
      return \is_subclass_of(self::checkNS($type), '\\cStruct\\' . $typeName);
      
   }

   /**
    * create a anon class at runtime
    */

   final static function define($class, $typedef)
   {  // ok to use eval since all parameters would be supplied by the programmer
		$caller = \get_called_class();
		
      $typedef = trim($typedef);
      // simple definition
      if (strpos($typedef, ';') === false)
      {  $typedef = self::checkNS($typedef);

         if (eval( "class " . $class . ' extends ' . $typedef . ' {};')  !== false)
         {
         	return;
         }
         
         throw new \Exception("There was a problem with your type definition."); 
      }

      // complex definition
				
		// trim any comments that might be in definition
		$typedef = preg_replace("%//.*\n%U","\n", $typedef);				
      
      if (
			eval("class " . $class . ' extends ' . $caller . ' {
         static protected $typedef = "'. $typedef
         . '"; };') !== false
		)
      {
      	return;
      }
		      
      throw new \Exception("There was a problem with your type definition.");
   }

   /**
    * Checks to make sure the type is in the right namespace
    */
   final static function checkNS($type)
   {  $arrCount = false;

      if (is_array($type))
      {
         $arrCount = (int)$type[1];
         $type = $type[0];

      }

      if (!class_exists($type) && ( \strpos('\\', $type) === false))
      {
         if (!class_exists('cStruct\\'. $type))
         {
            throw new \Exception("$type not defined");
         }

         $type = '\\cStruct\\'. $type;
      }

      if ($arrCount !== false)
      {
         $type = array($type, (int)$arrCount);
      }

      return $type;
   }

 	/**
    * Checks array type incase it wasn't defined correctly
    */
   protected static function checkType($type)
   {
      if (is_array($type))
      {
         if ((count($type) != 2) && (count($type) != 3))
         {  throw new \Exception("Malformed type definition near: " . print_r($type,true));
         }

      }
      return $type;

   }      
   
}


abstract class cStructBase extends cPrimitive
{

   /**
    * \arrayof{\string} All the strings for the various children of cStruct will live here
    * since we're doing static stuff
    */
   protected static $_packStr = array();

   /**
    * \arrayof{\int} similar to $_packStr.. another array
    */
   protected static $_byteLen = array();

   /**
    * \arrayof{\string} collection of unpack strings
    */
   protected static $_unpackStr = array();

   /**
    * \type{\arrayof{\string}} the type definition for each structure type
    * this must be supplied when you define the class
    */
   protected static $typedef = array();

   //abstract static function getPackStr();
   //abstract static function getUnackStr();
   //abstract static function sizeOf();

   /**
    * Creates an object using values
    * @param various \mixed
    * - If one parameter, and is an array, will use array as values
    * - If one parameter is a string, will use that as the values
    * - Otherwise, use all of the parameters listed as the values
    */
   public function __construct()
   {
      if ($args = \func_get_args())
      {
         if (count($args) == 1)
         {
            if (is_array($args[0]))
            {  $this->setValues($args);
            }
            elseif (is_string($args[0]))
            {
               $this->updateData($args[0]);
            }
         }
         else
         {
            $this->setValues($args);
         }
      }
      else
      {  $caller = \get_called_class();
         self::checkTypeDef($caller);

         $caller::resetData();

      }
   }

   /**
    * Create an object using a set of values (like construct)
    *
    * @param * mixed list of params
    *
    * @return type{cStruct} The new cStruct object
    */
   public static function newFromValues()
   {
      $args = \func_get_args();
      $caller = \get_called_class();
      return $caller::newFromArray($args);
   }

   /**
    * Create an object using an array of values.
    *
    * @param $array array array of params
    *
    * @return \type{cStruct} The new cStruct object
    */
   public static function newFromArray($array)
   {
      $caller = \get_called_class();

      $ret = new $caller();

      $ret->setValues($array);

      return $ret;
   }

   /**
    * Packs the string according to the pack format from getPackStr()
    *
    * @param $array \arrayof{\int} The data to pack
    * @return \string the packed byte string
    */
   public static function pack($array)
   {
      $caller = \get_called_class();

      $packArray = array();
      foreach ($array as $n => $v)
      {
         if (!is_array($v))
         {  $packArray[] = $v;
         }
         else
         {
            foreach($v as $n2 => $v2)
            {
               $packArray[] = $v2;
            }
         }
      }

      return \call_user_func_array('\pack',\array_merge(array($caller::getPackStr()),$packArray));
   }

   /**
    * Unpacks the provided string according to the typedef
    *
    * @param $str \string String of bytes to unpack
    * @return \array the unpacked data as an array
    */
   public static function unpack($str)
   {
      $strlen = strlen($str);
      $caller = \get_called_class();

      $len = $caller::sizeOf();


      if($len != strlen($str))
      {
         throw new \Exception( "String must be length of structure");
      }


      if ($ret = \unpack($caller::getUnpackStr(), $str))
      {
         return $ret;
      }

      throw new \Exception( "Unable to unpack with unpack string: ".$caller::getUnpackStr());

   }

  

   /**
    * Checks to make sure the type def is def'd
    */
    protected static function checkTypeDef($caller)
    {

      if (!isset($caller::$typedef))
      {  throw new \Exception("Type not defined");
      }

      if (is_string($caller::$typedef))
      {
         $caller::$typedef = self::stringToTypeDef($caller::$typedef);
      }
   }

   public static function transformUnpacked($unpacked, $caller = false)
   {
      if (!$caller)
      {  $caller = \get_called_class();
      }

      $ret = array();
      $structs = array();
      foreach($unpacked as $n => $v)
      {
         // handle arrays and nested structs (which are returned as arrays of chars)
         if (\preg_match('%:(\w+):(\d+)%', $n, $matches))
         {
            // load array with values
            if (\is_array($caller::$typedef[$matches[1]]))
            {
               $ret[$matches[1]][$matches[2]-1] = $v;
               continue;
            }

            // save chars as string for nested structs
            if ($matches[2] == 1)
            {  $ret[$matches[1]] = '';
               $structs[$matches[1]] =& $this->values[$matches[1]];
            }

            $ret[$matches[1]] .= chr($v);

            continue;
         }

         // clear out : : which is used to protect the names
         $n = preg_replace('%:(\w+):%', '$1', $n);

         if(array_key_exists($n, $caller::$typedef))
         {
            $type = self::checkNS($caller::$typedef[$n]);
				
            if (self::isType('cStructBase',$type)
               && class_exists($type))
            {
               $ret[$n] = $type::newFromData($v);
               continue;
            }

            // make sure we actually cut at null string.. unpack doesnt seem to do it

            if (is_array($type)
               //&& self::is_cPrimitive($type[0])
               && $type[0]::getPackStr() == 'a')
            {
               $v = rtrim($v, "\0");
            }

            $ret[$n] = $v;

         }
      }
      foreach ($structs as $n => $v)
      {  $type = self::checkNS($caller::$typedef[$n]);
         $ret[$n] = $type::newFromData($v);
      }
      return $ret;
   }
   
   static public function getTypeDef()
   {	$caller = \get_called_class();
   	
   	return $caller::$typedef;
   }
   
   protected static function recurseCallback($matches)
	{	
		$caller = \get_called_class();
		 
		 if (in_array($matches[3],array('struct','union' )))
		 {
			 $classname = '__'.get_called_class(). '_' . $matches[6];
	
            //$classname = '__' . $matches[6];

            $inner = preg_replace_callback('%((\w*)|(struct|union)\s*{(((?>[^{}])*|(?R))*)})\s*(\w*);%is',
                                    "$caller::recurseCallback", 
                                    $matches[4]); 
                                    
            
            if ($matches[3] == 'struct')
            {
            	self::define($classname, $inner);
            }
            else
            {
            	cUnion::define($classname, $inner);
            }
            
            return $classname . ' ' . $matches[6].';';            
	    }
	    else
	    {
	    	return $matches[0];
	    }
	            
		 
	}
  
	/**
    * Converts C-style type defs into array format needed for cStruct
    * @param $string \string The string to convert
    * @return \array array in cStruct typedef format
    */
   public static function stringToTypeDef($string)
   {	
   	$caller = \get_called_class();
      $string = \preg_replace('%(.*\*)%','POINTER ', $string);


     /* if(preg_match_all('%struct\s*\{(.*)\}\s*(\w+)\s*;%Usi', $string, $matches))
      {
         $count = count($matches[0]);

         for ($i = 0; $i < $count; $i++)
         {

            $classname = '__' . get_called_class() . '_' . $matches[2][$i];

            self::define($classname, $matches[1][$i]);

            $string = str_replace($matches[0][$i], " $classname " . $matches[2][$i] . ';', $string);
         }
      }*/

		$string = preg_replace_callback('%((\w*)|(struct|union)\s*{(((?>[^{}])*|(?R))*)})\s*(\w*);%is',
                                    "$caller::recurseCallback", 
                                    $string); 
		
      if (! \preg_match_all('%(\w+)\s*(\w+)\s*(\[(.*)\])?\s*;%U', $string, $matches))
      {
         throw new \Exception("Invalid type defintion");
      }

      $count = count($matches[0]);
      $newDef = array();

      for ($i = 0; $i < $count; $i++)
      {
         //$matches[1][$i] = strtoupper($matches[1][$i]);
         if (is_numeric($matches[4][$i]) && ($matches[4][$i] > 1))
         {
            $defType = array($matches[1][$i], $matches[4][$i]);
         }
         elseif (\defined($matches[4][$i]) && (constant($matches[4][$i]) > 1))
         {
            $defType = array($matches[1][$i], constant($matches[4][$i]));
         }
         else
         {
            $defType = $matches[1][$i];
         }

         $newDef[$matches[2][$i]] = $defType;
      }
				
      return $newDef;
   }


}
/**
 * Base class for structures
 *
 * example:
 * @code
 * C style definition:
 *
 * /*
 * typedef struct _DISPLAY_DEVICE {
 *   DWORD cb;
 *   TCHAR DeviceName[32];
 *   TCHAR DeviceString[128];
 *   DWORD StateFlags;
 *   TCHAR DeviceID[128];
 *   TCHAR DeviceKey[128];
 * }
 *
 * PHP style:
 *
 * class DISPLAY_DEVICE extends cStruct
 * {
 *    static $typedef = array(
 *       'cb'           => 'DWORD',
 *       'DeviceName'   => array('TCHAR', 32),
 *       'DeviceString' => array('TCHAR', 128),
 *       'StateFlags'   => 'DWORD',
 *       'DeviceID'     => array('TCHAR',128),
 *       'DeviceKey'    => array('TCHAR',128),
 *    );
 * };
 *
 * @endcode
 */

abstract class cStruct extends cStructBase
{

   /**
    * \type{\arrayof{\string}} the type definition for each structure type
    * this must be supplied when you define the class
    */
   protected static $typedef = array();

   /**
    * \type{\arrayof{\int}} the actual data stored with an instance of an item
    */
   protected $values = array();



   /**
    * Create an object using a string.
    *
    * @param $str \string array of params
    *
    * @return \type{cStruct} The new cStruct object
    */
   public static function newFromData($str)
   {
      $caller = \get_called_class();
      $strlen = \strlen($str);
      $len = $caller::sizeOf();

      if($len < $strlen)
      {
         $str = \substr($str,0, $len);
      }
      elseif($len > $strlen)
      {
         $str = \str_pad($str, $len, "\0");
      }

      $unpacked = $caller::unpack($str);

      $ret = new $caller();
      $ret->updateData($str);

      return $ret;
   }

   /**
    * Returns the pack string format
    * @return \string the format string for use with php's pack() function
    */
   public static function getPackStr($repeatCount = 1)
   {
      $caller = \get_called_class();


      if (array_key_exists($caller, self::$_packStr) && (self::$_packStr[$caller] !== ''))
      {
         return self::$_packStr[$caller] . ($repeatCount > 1 ? $repeatCount : '');
      }

      self::checkTypeDef($caller);

      self::$_packStr[$caller] = '';

      foreach($caller::$typedef as $var => $type)
      {
         $typeCount = 1;
         if (is_array($type = $caller::checkType($type)))
         {
            $typeCount = $type[1];
            $type = $type[0];
         }

         $type = self::checkNS($type);

         self::$_packStr[$caller] .= $type::getPackStr($typeCount);
      }

      return str_repeat(self::$_packStr[$caller], $repeatCount);
   }

   /**
    * Returns the number of bytes in the structure
    * @return \int the number of bytes in the structure
    */
   public static function sizeOf($count = 1)
   {
      $caller = \get_called_class();

      if (array_key_exists($caller, self::$_byteLen) && (self::$_byteLen[$caller] != 0))
      {  return self::$_byteLen[$caller] * $count;
      }

      self::checkTypeDef($caller);

      self::$_byteLen[$caller] = 0;

      foreach($caller::$typedef as $var => $type)
      {  $typeCount = 1;
         if (is_array($type = self::checkType($type)))
         {
            $typeCount = $type[1];
            $type = $type[0];
         }
         $type = self::checkNS($type);

         self::$_byteLen[$caller] += $type::sizeOf($typeCount);
      }

      return self::$_byteLen[$caller] * $count;
   }

   /**
    * Returns the unpack format string
    * @return \string the format string for use with php's unpack() function
    */
   public static function getUnpackStr()
   {
      $caller = \get_called_class();

      if (array_key_exists($caller, self::$_unpackStr) && (self::$_unpackStr[$caller] !== ''))
      {  return self::$_unpackStr[$caller];
      }

      self::checkTypeDef($caller);

      $unPackArr = array();
      foreach($caller::$typedef as $var => $type)
      {  $typeCount = 1;
         if (is_array($type = self::checkType($type)))
         {
            $typeCount = $type[1];
            $type = $type[0];

         }

         $type = self::checkNS($type);

         if ( $type::isType('cStruct') )
         {
            $typeCount = $type::sizeOf();
            $type = "cStruct\STRUCT";

         }
         elseif ( $type::isType('cUnion') )
         {
            $typeCount = $type::sizeOf();
            $type = "cStruct\UNION";

         }
         $unPackArr[] =  $type::getPackStr($typeCount) . ':'    . $var . ':' ;
      }

      self::$_unpackStr[$caller] = implode('/', $unPackArr);
      return self::$_unpackStr[$caller];
   }



   /**
    * Updates the data with a bytestring
    * @param $str \string byte string with which to update the internal values
    */
   public function updateData($str)
   {
      $caller = \get_called_class();

      if (!$str)
      {  $caller::resetData();
      }

      $unpacked = $caller::unpack($str);

      $this->values = $caller::transformUnpacked($unpacked);
   }

   /**
    * Clears all data from the structure
    */
   public function resetData()
   {
      $caller = \get_called_class();
      self::checkTypeDef($caller);

		

      foreach ($caller::$typedef as $name => $type )
      {	
      	
         if (!is_array($type = $caller::checkType($type)))
         {	$type = $caller::checkNS($type);
         
	         if ($type::isType('cStructBase', $type))
	         {
					$blank = new $type();
				}
				else
				{	
	         	$blank = 0;
	         }
            
            $this->values[$name] = $blank;
            
            
            continue;
         }
         $type[0] = self::checkNS($type[0]);

         if (strtolower($type[0]::getPackStr() == 'a'))
         {
            $this->values[$name] = '';
            continue;
         }

         $this->values[$name] = array_fill(0, $type[1], ($type[0]::isType('cStructBase', $type[0]) ? new $type[0]() : 0));
      }
   }
   /**
    * Returns a byte string for use with WinAPI functions
    * @return \string byte string for use with WinAPI functions
    */
   public function getData()
   {
      $caller = \get_called_class();
      if (isset($this->values) && is_array($this->values))
      {
         return $caller::pack($this->values);
      }

      throw new \Exception("Trying to get data when value is not set");
   }

   /**
    * Alias for getData();
    * @return \string byte string for use with WinAPI functions
    */

   public function __toString()
   {
      return $this->getData();
   }

   /**
    * Returns a value stored in the structure
    * @param $valueName \string key name of data to return
    * @return \mixed value of data (usually should be int)
    */
   public function __get($valueName)
   {
      $caller = \get_called_class();

      if (!isset($caller::$typedef))
      {
         throw new \Exception("Type for class $caller not defined");
      }

      if (! \array_key_exists($valueName, $caller::$typedef))
      {
         throw new \Exception("Value '$valueName' does not exist in type definition for '$caller'");
      }

      if (\array_key_exists($valueName, $this->values))
      {

         return $this->values[$valueName];
      }

      return null;

   }

   /**
    * Sets a value in the stucture (key must be listed in the typedef)
    * @param $valueName \string key name to update
    * @param $value \int value to set
    */
   public function __set($valueName, $value)
   {
      $caller = \get_called_class();

      if (!isset($caller::$typedef))
      {
         throw new \Exception("Type for class $caller not defined");
      }

      if (! \array_key_exists($valueName, $caller::$typedef))
      {
         throw new \Exception("Value $valueName does not exist in type defintion");
      }

      if (!is_array($caller::$typedef[$valueName])      		      	         
         // handle array
         ||	(is_array($value)          	
         	&& count($value) == $caller::$typedef[$valueName][1]
         )
         // handle string
         || (is_string($value)         	
	      	&& strlen($value) <= $caller::$typedef[$valueName][1]
	      		// str_pad isn't really going to return, just doing something that should only
	      		// be done for strings
	      	&& $value = str_pad($value, "\0", $caller::$typedef[$valueName][1])
      	)
      )		
      {
         $this->values[$valueName] = $value;
         return;
      }

      throw new \Exception("Error assigning value to struct");
   }

   /**
    * Sets data with an array
    * @param $array \array Array of values
    */
   public function setValues($array)
   {
      $caller = \get_called_class();

      if (!isset($caller::$typedef))
      {
         throw new \Exception("Type not defined");
      }

      $count = \count($caller::$typedef);

      if( $count != \count($array))
      {  throw new \Exception("Provided array size does not match structure size");
      }


      $keys = \array_keys($caller::$typedef);

      for ($i =0; $i < $count; $i++)
      {
         $this->values[$keys[$i]] = $array[$i];
      }

   }

   /**
    * Returns an array of values
    * @return \array the values
    */
   public function getValues()
   {
      return $this->values;
   }

	
   
};

/**
 * cUnion is a structure like cStruct, but a little bit different
 *
 *
 */
abstract class cUnion extends cStructBase
{

   /**
    * \type{\arrayof{\string}} the type definition for each structure type
    * this must be supplied when you define the class
    */
   protected static $typedef = array();

   /**
    * \string the actual data stored with an instance of the union
    */
   protected $contents = '';

   public function __construct($data = '')
   {
      $caller = \get_called_class();
      
      self::checkTypeDef($caller);
      
      $size = $caller::sizeOf();
		
      $this->contents = substr(str_pad($data, $size, "\0"), 0, $size);

      //$args = func_get_args();
      //call_user_func('parent::__construct',$args);
   }

   static function getPackStr()
   {
      return;
   }

   static function getUnpackStr()
   {
      return;
   }

   public static function newFromData($data)
   {  $caller = \get_called_class();

      return new $caller($data);

   }
   public static function sizeOf($count = 1)
   {
      $caller = \get_called_class();

      // check to see if we already defined this
      if (array_key_exists($caller, self::$_byteLen) && (self::$_byteLen[$caller] != 0))
      {  return self::$_byteLen[$caller] * $count;
      }

      self::checkTypeDef($caller);

      self::$_byteLen[$caller] = 0;

      foreach($caller::$typedef as $var => $type)
      {  $typeCount = 1;
         if (is_array($type = self::checkType($type)))
         {
            $typeCount = $type[1];
            $type = $type[0];
         }
         $type = self::checkNS($type);

         // union.. return the largest member
         if ( $type::sizeOf($typeCount) > self::$_byteLen[$caller])
         {  self::$_byteLen[$caller] = $type::sizeOf($typeCount);
         }
      }

      return self::$_byteLen[$caller] * $count;
   }


   /**
    * Returns a value stored in the union
    * @param $valueName \string key name of data to return
    * @return \mixed value of data (usually should be int)
    */
   public function __get($valueName)
   {
      $caller = \get_called_class();

      if (!isset($caller::$typedef))
      {
         throw new \Exception("Type for class $caller not defined");
      }

      if (! \array_key_exists($valueName, $caller::$typedef))
      {
         throw new \Exception("Value '$valueName' does not exist in type definition for '$caller'");
      }

      $type = $caller::$typedef[$valueName];

      if (is_array($type = $caller::checkType($type)))
      {
         $typeCount = $type[1];
         $type = $type[0];
      }

      $type = self::checkNS($type);

      if ($type::isType('cPrimitive')
         && !$type::isType('cStructBase')
      )
      {
         $unpackStr = $type::getPackStr($typeCount) . ':' . $valueName . ':';
         $unpacked = \unpack($unpackStr, $this->contents);
         $unpacked = $caller::transformUnpacked($unpacked);
         return $unpacked[$valueName];
      }
      else
      {  return $type::newFromData($this->contents);
      }
   }

	/**
	 * Pack a string or array
	 * @param $str \mixed string or array to pack
	 * @param $valueName \string name of key of value
	 * @returns \array the unpacked array
	 */
   public static function unpack($str, $valueName)
   {

      $caller = \get_called_class();

      $type = $caller::$typedef[$valueName];

      $typeCount = 1;

      if (is_array($type = $caller::checkType($type)))
      {
         $typeCount = $type[1];
         $type = $type[0];
      }

      $type = self::checkNS($type);

      if ($type::isType('cPrimitive')
         && !$type::isType('cStructBase')
      )
      {
         $unpackStr = $type::getPackStr($typeCount) . ':' . $valueName . ':';

      }
      else
      {  $unpackStr = $type::getUnpackStr($typeCount);
      }

      return \unpack($unpackStr, $str);



   }
   /**
    * Sets a value in the stucture (key must be listed in the typedef)
    * @param $valueName \string key name to update
    * @param $value \int value to set
    */
   public function __set($valueName, $value)
   {

      $caller = \get_called_class();

      if (!isset($caller::$typedef))
      {
         throw new \Exception("Type for class $caller not defined");
      }

      if (! \array_key_exists($valueName, $caller::$typedef))
      {
         throw new \Exception("Value $valueName does not exist in type defintion");
      }

      $this->contents = self::pack($value, $caller::$typedef[$valueName]);

      return;

   }

   /**
    * Packs the string according to the type
    */
   public static function pack($str, $type)
   {
      $caller = \get_called_class();
      $typeCount = 1;
      $packArr = array();

      $type = self::checkNS($type);

      if (is_array($type = $caller::checkType($type)))
      {
         if (!is_array($str))
         {
            throw new \Exception("Array expected");
         }

         $count = count($str);

         $typeCount = $type[1];
         $type = $type[0];
         $type = self::checkNS($type);

         for($i = 0; $i < $count; $i++)
         {
            if (is_string($str[$i]) && $type::getPackStr() != 'a')
            {  $str[$i] = ord($str[$i]);
            }

            $packArr[] = $str[$i];
         }
      }
      else
      {
         $packArr[] = $str;
      }

      if ($type::isType('cPrimitive')
         && !$type::isType('cStruct'))
      {
         return \call_user_func_array('\pack',\array_merge(array($type::getPackStr($typeCount)),$packArr));
      }

      return $type::newFromData($str);
   }

}

// first order primitives

class CHAR extends cPrimitive
{
   protected static $_packStr = 'c';
   protected static $_byteLen = 1;
};

class FLOAT extends cPrimitive
{
   protected static $_packStr = 'f';
   protected static $_byteLen = 4;
};

class UCHAR extends cPrimitive
{
   protected static $_packStr = 'C';
   protected static $_byteLen = 1;
};

class INT extends cPrimitive
{
   protected static $_packStr = 'i';
   protected static $_byteLen = 4;
};

class UINT extends cPrimitive
{
   protected static $_packStr = 'I';
   protected static $_byteLen = 4;
};

class LONG extends cPrimitive
{
   protected static $_packStr = 'l';
   protected static $_byteLen = 4;
};

class ULONG extends cPrimitive
{
   protected static $_packStr = 'L';
   protected static $_byteLen = 4;
};

class SHORT extends cPrimitive
{
   protected static $_packStr = 'S';
   protected static $_byteLen = 2;
};

class USHORT extends cPrimitive
{
   protected static $_packStr = 's';
   protected static $_byteLen = 2;
};

class LONGLONG extends cPrimitive
{
   protected static $_packStr = 'd'; // not sure if d is for signed or unsigned
   protected static $_byteLen = 8;

}

class ULONGLONG extends cPrimitive
{
   protected static $_packStr = 'd';
   protected static $_byteLen = 8;
};

class TCHAR extends cPrimitive
{
   protected static $_packStr = 'a';
   protected static $_byteLen = 1;
};

class STRUCT extends cPrimitive
{
   protected static $_packStr = 'a';
   protected static $_byteLen = 1;
};

class UNION extends cPrimitive
{
   protected static $_packStr = 'a';
   protected static $_byteLen = 1;
};

// Specially defined primitives
class POINTER                 extends ULONG {};
//class STRUCT                extends TCHAR {};

// second order primitives

class BYTE                    extends UCHAR {};
class CALLBACK                extends POINTER {};
class DWORD                   extends ULONG {};
class HANDLE                  extends POINTER {};
class WORD                    extends USHORT {};

// third order

class APIENTRY                extends CALLBACK {};
class ATOM                    extends WORD {};

class BOOL                    extends INT {};
class BOOLEAN                 extends BYTE {};

class COLORREF                extends DWORD {};

class DWORD_PTR               extends ULONG_PTR {};
class DWORD32                 extends ULONG {};
class DWORD64                 extends ULONGLONG {};

class HACCEL                  extends HANDLE {};
class HALF_PTR                extends HANDLE {};
class HBITMAP                 extends HANDLE {};
class HBRUSH                  extends HANDLE {};
class HCOLORSPACE             extends HANDLE {};
class HCONV                   extends HANDLE {};
class HCONVLIST               extends HANDLE {};
class HCURSOR                 extends HANDLE {};
class HDC                     extends HANDLE {};
class HDDEDATA                extends HANDLE {};
class HDESK                   extends HANDLE {};
class HDROP                   extends HANDLE {};
class HDWP                    extends HANDLE {};
class HENHMETAFILE            extends HANDLE {};
class HFILE                   extends HANDLE {};
class HFONT                   extends HANDLE {};
class HGDIOBJ                 extends HANDLE {};
class HGLOBAL                 extends HANDLE {};
class HHOOK                   extends HANDLE {};
class HICON                   extends HANDLE {};
class HINSTANCE               extends HANDLE {};
class HKEY                    extends HANDLE {};
class HKL                     extends HANDLE {};
class HLOCAL                  extends HANDLE {};
class HMENU                   extends HANDLE {};
class HMETAFILE               extends HANDLE {};
class HMODULE                 extends HANDLE {};
class HMONITOR                extends HANDLE {};
class HPALETTE                extends HANDLE {};
class HPEN                    extends HANDLE {};
class HRGN                    extends HANDLE {};
class HRSRC                   extends HANDLE {};
class HSZ                     extends HANDLE {};
class HWINSTA                 extends HANDLE {};
class HWND                    extends HANDLE {};
class HRESULT                 extends LONG {};

class INT_PTR                 extends INT {};
class INT32                   extends INT {};
class INT64                   extends LONGLONG {};

class LANGID                  extends WORD {};
class LCID                    extends DWORD {};
class LCTYPE                  extends DWORD {};
class LGRPID                  extends DWORD {};
class LONG_PTR                extends LONG {};
class LONG32                  extends LONG {};
class LONG64                  extends LONGLONG {};
class LPARAM                  extends LONG {};
class LPBOOL                  extends POINTER {};
class LPBYTE                  extends POINTER {};
class LPCOLORREF              extends POINTER {};
class LPCSTR                  extends POINTER {};
class LPCTSTR                 extends POINTER {};
class LPCVOID                 extends POINTER {};
class LPCWSTR                 extends POINTER {};
class LPDWORD                 extends POINTER {};
class LPHANDLE                extends POINTER {};
class LPINT                   extends POINTER {};
class LPLONG                  extends POINTER {};
class LPSTR                   extends POINTER {};
class LPTSTR                  extends POINTER {};
class LPVOID                  extends POINTER {};
class LPWORD                  extends POINTER {};
class LPWSTR                  extends POINTER {};
class LRESULT                 extends POINTER {};

class PBOOL                   extends POINTER {};
class PBOOLEAN                extends POINTER {};
class PBYTE                   extends POINTER {};
class PCHAR                   extends POINTER {};
class PCSTR                   extends POINTER {};
class PCTSTR                  extends POINTER {};
class PCWSTR                  extends POINTER {};
class PDWORD                  extends POINTER {};
class PDWORDLONG              extends POINTER {};
class PDWORD_PTR              extends POINTER {};
class PDWORD32                extends POINTER {};
class PDWORD64                extends POINTER {};
class PFLOAT                  extends POINTER {};
class PHALF_PTR               extends POINTER {};
class PHANDLE                 extends POINTER {};
class PHKEY                   extends POINTER {};
class PINT                    extends POINTER {};
class PINT_PTR                extends POINTER {};
class PINT32                  extends POINTER {};
class PINT64                  extends POINTER {};
class PLCID                   extends POINTER {};
class PLONG                   extends POINTER {};
class PLONGLONG               extends POINTER {};
class PLONG_PTR               extends POINTER {};
class PLONG32                 extends POINTER {};
class PLONG64                 extends POINTER {};
class POINTER_32              extends POINTER {};
class POINTER_64              extends POINTER {};
class POINTER_SIGNED          extends POINTER {};
class POINTER_UNSIGNED        extends POINTER {};
class PSHORT                  extends POINTER {};
class PSIZE_T                 extends POINTER {};
class PSSIZE_T                extends POINTER {};
class PSTR                    extends POINTER {};
class PTBYTE                  extends POINTER {};
class PTCHAR                  extends POINTER {};
class PTSTR                   extends POINTER {};
class PUCHAR                  extends POINTER {};
class PUHALF_PTR              extends POINTER {};
class PUINT                   extends POINTER {};
class PUINT_PTR               extends POINTER {};
class PUINT32                 extends POINTER {};
class PUINT64                 extends POINTER {};
class PULONG                  extends POINTER {};
class PULONGLONG              extends POINTER {};
class PULONG_PTR              extends POINTER {};
class PULONG32                extends POINTER {};
class PULONG64                extends POINTER {};
class PUSHORT                 extends POINTER {};
class PVOID                   extends POINTER {};
class PWCHAR                  extends POINTER {};
class PWORD                   extends POINTER {};
class PWSTR                   extends POINTER {};

class SC_HANDLE               extends HANDLE {};
class SC_LOCK                 extends POINTER {};
class SERVICE_STATUS_HANDLE   extends HANDLE {};
class SIZE_T                  extends ULONG_PTR {};
class SSIZE_T                 extends LONG_PTR {};

class TBYTE                   extends TCHAR {};

class UHALF_PTR               extends SHORT {};
class UINT_PTR                extends UINT {};
class UINT32                  extends UINT {};
class UINT64                  extends ULONGLONG {};

class ULONG_PTR               extends ULONG {};
class ULONG32                 extends ULONG {};
class ULONG64                 extends ULONGLONG {};

class USN                     extends LONGLONG {};

class VOID                    extends cPrimitive {};

class WCHAR                   extends USHORT {}; // 16 bit unicode character
class WINAPI                  extends CALLBACK {};
class WPARAM                  extends UINT {};


class UNICODE_STRING          extends cStruct
{
   static protected $typedef = array(
     'Length'        => 'USHORT',
     'MaximumLength' => 'USHORT',
     'Buffer'        => 'PWSTR',
  );
}

class_alias('cStruct\cStruct', 'cStruct');
class_alias('cStruct\cStruct', 'cUnion');


function rtrim($string)
{
	if ($loc = strpos($string, "\0"))
	{	$string = substr($string, 0, $loc);
	}
	
	return $string;
}

} // close namespace

namespace {

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
     trigger_error("PHP 5.3+ Reqired for cStruct", E_USER_ERROR);
}

}
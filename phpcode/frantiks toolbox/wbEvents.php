<?php

/**
*  wbEventHandler v0.91 by frantik 2009
*  Light framework for handling events within WinBinder
*/

class wbEventHandler
{   
   protected static $windows = array();
   protected static $controls = array();
   protected static $ids = array();
   protected static $counter = 1000;
   protected static $genericHandler = false;
   
   // fail if class is attempted to be constructed
   public function __construct()
   {  trigger_error("Failed to construct wbEventHandler because it is a static class", E_USER_ERROR);
      exit;
   }
           
   // adds existing window to event handler
   public static function createWindowEvent($handler, $window)
   { 
      wb_set_handler($window, '__wbEventHandler');
      self::$windows[$window] = $handler;     
      return $window;
   }
   
   // adds existing control to event handler
   public static function createControlEvent($handler, $control)
   { 
      self::$controls[$control] = $handler;     
      return $control;
   }
 
   
   // Used when adding a ID handler.  If no ID specified, one will be generated
   // Returns ID for use with wb_create_control
   public static function createIdEvent($handler, $id = false)
   {    
      if ($id === false)
      {   $id = self::$counter++;
      }
      
      self::$ids[$id] = $handler;
      return $id;
   }
   
   // returns an ID based on handler
   public static function getId($handler)   
   {
   	return array_search($handler, self::$ids);
   }
   
   // creates a generic listener to catch uncaught events
   public static function createGenericEvent($handler)
   {             
      self::$genericHandler = $handler; 
      return true;
   }
   
   
                               
   // Handles event calls (called by __wbEventHandler)
   public static function eventHandler($window, $id, $ctrl, $param1, $param2)
   {    
     
      //file_put_contents('out', "$window, $id, $ctrl, $param1, $param2" . print_r(self::$controls,true), FILE_APPEND);
     
      $params = array('window'=>$window, 'id'=>$id, 'ctrl'=>$ctrl, 'param1'=>$param1, 'param2'=>$param2, 'mouse'=>false);
         
      // Generate some extra data about the mouse
      if ($id == IDDEFAULT)     
      {
         $params['mouse']['xpos'] = $param2 & 0xFFFF;
         $params['mouse']['ypos'] = ($param2 & 0xFFFF0000) >> 16;
         
         $params['mouse'] = array('buttons' => array( 'LBUTTON' => false,
                                                      'RBUTTON' => false,
                                                      'MBUTTON' => false,
                                                      'CONTROL' => false,
                                                      'SHIFT'   => false,
                                                      'ALT'     => false));
         
         if($param1 & WBC_LBUTTON)  { $params['mouse']['buttons']['LBUTTON'] = true; }
         if($param1 & WBC_RBUTTON)  { $params['mouse']['buttons']['RBUTTON'] = true; }
         if($param1 & WBC_MBUTTON)  { $params['mouse']['buttons']['MBUTTON'] = true; }
         if($param1 & WBC_ALT)      { $params['mouse']['buttons']['ALT']     = true; }
         if($param1 & WBC_CONTROL)  { $params['mouse']['buttons']['CONTROL'] = true; }
         if($param1 & WBC_SHIFT)    { $params['mouse']['buttons']['SHIFT']   = true; }                           
      }
         
      // Call event function
      if (array_key_exists($id, self::$ids) && is_callable(self::$ids[$id]))     
      {	return call_user_func(self::$ids[$id], $params);         
      }      
      elseif (array_key_exists($ctrl, self::$controls) && is_callable(self::$controls[$ctrl]))     
      {	return call_user_func(self::$controls[$ctrl], $params);       
      }      
      elseif (array_key_exists($window, self::$windows) && is_callable(self::$windows[$window]))     
      {	return call_user_func(self::$windows[$window], $params);
      }      
      elseif (is_callable(self::$genericHandler))
      {	return call_user_func(self::$genericHandler, $params);
      }
   }         
   
   // alias for wb_main_loop()
   public static function waitForEvents()
   {  wb_main_loop();
   }
      // end class declaration
}

// wrapper function because wb_set_handler can't call class functions
function __wbEventHandler($window, $id, $ctrl, $param1, $param2)
{
   wbEventHandler::eventHandler($window, $id, $ctrl, $param1, $param2);
}

<?php

namespace PHPUi;

final class Utils 
{
    
    protected static $_value_array = array();
    
    protected static $_replace_array_keys = array();
       
    /**
     * Encode a given object to JSON format, ensure that functions won't be encoded
     * 
     * @param mixed $array
     * @return string 
     */
    public static function encodeJSON($array)
    {
        if(!is_array($array) && !is_object($array)) {
            $array = array($array);
        }
     
        self::encodeArray($array);

        // Now encode the array to json format
        $json = json_encode($array, JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

        $json = str_replace(self::$_replace_array_keys, self::$_value_array, $json);
        
        return $json;
    }
    
    public static function encodeValueJSON($value)
    {
        if(!is_array($value) && strpos(trim($value), 'function(') === 0) {
            return "\"".str_replace("\"", "'", trim($value))."\"";
        } else if(is_array($value)) {
            foreach($value as $key => $val) {
                $value[$key] = self::encodeValue($val);
            }
            return $value;
        } else 
            return $value;
    }
    
    /**
     * Performs the JSON encoding on a given array
     * 
     * @param array $array 
     */
    private static function encodeArray(&$array)
    {
          foreach($array as $key => &$value) {
              // Look for values starting with 'function('
              if(!is_array($value) && strpos($value, 'function(') === 0) {
                // Store function string.
                self::$_value_array[] = $value;
                // Replace function string in $foo with a 'unique' special key.
                $value = '%' . $key . '%';
                // Later on, we'll look for the value, and replace it.
                self::$_replace_array_keys[] = '"' . $value . '"';
              } else if(is_array($value)) {
                  self::encodeArray($value);
              }
        }
    }
    
    /**
     * Decode a given string in JSON format to a PHP array
     * 
     * @param string $content
     * @return array 
     */
    public static function decodeJSON($content)
    {
        // Decode the JSON string to an associative array
        $array = json_decode($content, true);
        
        if(is_array($array)) 
            return $array;
        else
            return null;
    }
    
    /**
     * Count the occurences of the given key in the array
     * 
     * @param array $array
     * @param mixed $key
     * @return int 
     */
    public static function countKey($array, $key)
    {
        $count = 0;
        foreach($array as $k => $val)
            if($k == $key)
                $count++; 
        return $count;
    }
    
}

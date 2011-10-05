<?php

final class PHPUi_JS
{
    
    protected static $_value_arr = array();
    
    protected static $_replace_keys = array();
    
    /**
     * Encode a given array to JSON format, ensure that functions won't be encoded
     * 
     * @param array $array
     * @return string 
     */
    public static function encode($array)
    {
        if(!is_array($array)) {
            $array = array($array);
        }
     
        self::encodeArray($array);

        // Now encode the array to json format
        $json = json_encode($array);

        $json = str_replace(self::$_replace_keys, self::$_value_arr, $json);
        
        return $json;
    }
    
    /**
     * Performs the encoding
     * 
     * @param array $array 
     */
    protected static function encodeArray(&$array)
    {
          foreach($array as $key => &$value) {
              // Look for values starting with 'function('
              if(!is_array($value) && strpos($value, 'function(') === 0) {
                // Store function string.
                self::$_value_arr[] = $value;
                // Replace function string in $foo with a 'unique' special key.
                $value = '%' . $key . '%';
                // Later on, we'll look for the value, and replace it.
                self::$_replace_keys[] = '"' . $value . '"';
              } else if(is_array($value)) {
                  self::encodeArray($value);
              }
        }
    }
    
}


?>

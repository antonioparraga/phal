<?php

class __ParameterSplitter {

    /**
     * This rather nasty looking function takes an string representing a list or parameters (i.e. name="caption" value="click here" ...)
     * and puts them into an associative array.
     * 
     * @param string $params_str The string to split into parameters
     * @return array An associative array of pairs [key, value]
     */ 
    static public function splitParameters($params_str) {
        $params_str = trim($params_str);
        $length = strlen($params_str);
        $return_value = array();
        $key = '';
        $val = '';
        $single_quote_hex = "\x27";
        $double_quote_hex = "\x22";
        $done = false;
        $remove_spaces = false;
        /* $mode:
          0 - search for key
          1 - search for =
          2 - search for end
          3 - search for end with quotes */
        $mode = 0;
        $quote_character = null;
        for ($x = 0; $x < $length; $x++) {
            $chr = substr($params_str, $x, 1);
            if(preg_match('/\s/', $chr)) {
                $is_space = true;
            }
            else {
                $is_space = false;
            }
            if ($remove_spaces == false || !$is_space) {
                $remove_spaces = false; //in any case
                switch ($mode) {
                    //initial state:
                    case 0:
                        if ( !$is_space ) {
                            $key .= $chr;
                        }
                        $mode = 1;
                        break;
                    //scan the key:
                    case 1:
                        if ($chr != '=') {
                            if($is_space) {
                                $remove_spaces = true;  
                            }
                            else {
                                $key .= $chr;
                            }
                        }
                        else {
                            $mode = 2;
                            $remove_spaces = true;
                        }
                        break;
                    //scan the value:
                    case 2:
                        if (($chr == "\"" || $chr == "'") && ($val == '')) {
                            $quote_character = $chr;
                            $mode = 3;
                        } else {
                            if ( $is_space || ($x == ($length - 1)) ) {
                                $done = true;
                                if (! $is_space ) {
                                    $val .= $chr;
                                }
                            } else {
                                $val .= $chr;
                            }
                        }
                        break;
                    case 3:
                        if (($chr == $quote_character)||($x == ($length - 1))) {
                            $done = true;
                            if ($chr != $quote_character) {
                                $val .= $chr;
                            }
                        } else {
                            $val .= $chr;
                        }
                    break;
                }
            }
            
            if ($done == true) {
                $mode = 0;
                $done = false;
                //remove delimiter quotes if exists: 
                $key = trim(strtolower($key));
                switch($quote_character) {
                    case '"':
                        $val = trim($val, $double_quote_hex);
                        break;
                    case "'":
                        $val = trim($val, $single_quote_hex);
                        break;
                }
                $return_value[$key] = $val;
                $key = '';
                $val = '';
            }
        }
        return $return_value;
    }
    
    
}
